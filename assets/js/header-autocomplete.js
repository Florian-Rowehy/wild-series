const searchBar = document.getElementById('searchBar');
const listFoundFilms = document.getElementById('listFoundFilms');

searchBar.addEventListener('input', (event)=>{
    const input = event.target.value;
    if (!input) {
        listFoundFilms.innerHTML = '';
        listFoundFilms.classList.add('d-none')
        return;
    }
    const url = '/programs/search/'+ input;
    fetch(url)
        .then(response => response.json())
        .then(programs => {
            if (programs.length !== 0) {
                listFoundFilms.classList.remove('d-none');
                listFoundFilms.replaceChildren(
                    ...programs.map(program => {
                        const li = document.createElement('li');
                        const anchor = document.createElement('a');
                        anchor.href = '/programs/' + program.slug;
                        anchor.textContent = program.title;
                        li.append(anchor);
                        return li;
                    })
                );
            } else {
                listFoundFilms.innerHTML = '';
                listFoundFilms.classList.add('d-none');
            }
        })
        .catch(err => {
            console.log(err);
        });
});
