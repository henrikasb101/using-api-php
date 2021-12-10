const GUI = {
    loader: document.getElementById('loader'),
    form: document.querySelector('.panel form'),
    input: document.querySelector('.panel form input'),
};

GUI.form.addEventListener('submit', () => {
    if (!GUI.input.hasAttribute('readonly')) {
        GUI.input.setAttribute('readonly', '');
    }

    if (GUI.loader.classList.contains('visually-hidden')) {
        GUI.loader.classList.remove('visually-hidden');
    }
});
