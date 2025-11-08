require("./bootstrap");
import Echo from "laravel-echo";
import Pusher from "pusher-js";

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: "pusher",
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    forceTLS: false,
    wsHost: window.location.hostname,
    wsPort: 6001,
    disableStats: true,
});

window.Echo.channel("uploads").listen("UploadProcessed", (e) => {
    console.log("Upload update", e);
    // update tabel atau UI status
    let row = document.querySelector(`#upload-${e.id} .status`);
    if (row) row.innerText = e.status;
});
