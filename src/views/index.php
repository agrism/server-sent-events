Events

<ul id="list">
	<li>aaa</li>
</ul>

<script>
    if (window.EventSource !== undefined) {
        const evtSource = new EventSource("<?=env('HOST')?>/server/stream");

        evtSource.onmessage = function (event) {
            const newElement = document.createElement("li");
            const eventList = document.getElementById("list");

            var data = JSON.parse(event.data)
            console.log(data);
            newElement.innerHTML = "message: " + data.message;
            eventList.appendChild(newElement);
        }
    } else {
        alert("SSE is not supported in this browser!");
    }


</script>