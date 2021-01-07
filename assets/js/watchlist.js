document.querySelector("#watchlist").addEventListener('click', addToWatchlist);

function addToWatchlist(event) {
    const watchlistIcon = event.target;
    const link = watchlistIcon.dataset.href;
    fetch(link)
        .then(res => res.json())
        .then(function(res) {
            if (res.isInWatchlist) {
                watchlistIcon.classList.remove('far');
                watchlistIcon.classList.add('fas');
            } else {
                watchlistIcon.classList.remove('fas');
                watchlistIcon.classList.add('far');
            }
        })
        .catch(err=>{console.log(err)});
}