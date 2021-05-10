document.addEventListener("DOMContentLoaded", () => {
    fetch('https://freegeoip.app/json/90.108.53.14' )
    .then(
        (response) => {
            response.json()
            .then(
                (json) => {
                    console.dir(json);
                })
        })
})