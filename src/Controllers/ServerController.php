<?php

namespace App\Controllers;

use App\Db\Db;
use PDO;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ServerController extends Controller
{
	public function stream(): StreamedResponse
	{
		$streamedResponse = new StreamedResponse;
		$streamedResponse->headers->set('Content-Type', 'text/event-stream');
		$streamedResponse->headers->set('Cache-Control', 'no-cache');
		$streamedResponse->headers->set('Connection', 'keep-alive');
		$streamedResponse->headers->set('X-Accel-Buffering', 'no');

		$streamedResponse->setCallback(function () {
			// if the connection has been closed by the client we better exit the loop
			if (connection_aborted()) {
				return;
			}

			echo ':'.str_repeat(' ', 2048)."\n"; // 2 kB padding for IE
			echo "retry: 5000\n";

			$clientId = $this->getClientId();

			$sql = <<<SQL
			SELECT e.*, ce.id as client_event_id, ce.delivered, ce.client_id FROM events e 
			LEFT JOIN client_events ce ON e.id = ce.event_id AND ce.client_id = ?
			ORDER BY ce.created_at desc
SQL;

			$statement = Db::factory()->prepare($sql);
			$statement->execute([$clientId]);

			$eventObject = null;

			foreach ($statement->fetchAll(PDO::FETCH_OBJ) as $row) {
				if (!$row->delivered) {
					$eventObject = $row;
					break;
				}
			}

			if (!$eventObject) {
				// no new data to send
				echo ": heartbeat\n\n";
			} else {
				$data = json_encode([
					'id' => $eventObject->id,
					'type' => $eventObject->type,
					'message' => $eventObject->message,
				]);

				echo 'id: '.$eventObject->id."\n";
				echo 'event: '.$eventObject->type."\n";
				echo 'data: '.$data."\n\n";

				if ($eventObject->delivered === null) {
					$statement = Db::factory()
						->prepare('INSERT INTO client_events (event_id, client_id, delivered) VALUES (?, ?, ?)');
					$statement->execute([$eventObject->id, $clientId, 1]);
				} else {
					$statement = Db::factory()
						->prepare('UPDATE client_events SET event_id=?, client_id = ?, delivered = ? WHERE id= ?');
					$statement->execute([$eventObject->id, $clientId, '1', $eventObject->client_event_id]);
				}
			}

			ob_flush();
			flush();
			sleep(5);
		});

		return $streamedResponse->send();
	}

	protected function getClientId(): string
	{
		return md5(php_uname('n').$_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR']);
	}

	public function addEvent()
	{
		$statement = Db::factory()->prepare('INSERT INTO events  (message, type) VALUES (?,?)');
		$message = uniqid();
		$statement->execute([$message, 'message']);
		$statement->execute();

		dd($message);
	}
}