const searchBar = document.getElementById('searchBar');
const listFoundFilms = document.getElementById('listFoundFilms');

searchBar.addEventListener('input', (event)=>{
    const input = event.target.value;
    const url = '/programs/search/'+ input;
    fetch(url)
        .then(response => response.json())
        .then(programs => {
            listFoundFilms.replaceChildren(
                ...programs.map( program => {
                    const li = document.createElement('li');
                    li.textContent = program.title;
                    return li;
                })
            );
        })
        .catch(err => {
            console.log(err);
        });
});
