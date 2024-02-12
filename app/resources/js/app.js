import './bootstrap';

const button_submit = document.querySelector('.button_form');
const loader = document.querySelector('.loader');
const button_form_stop = document.querySelector('.button_form_stop');
const question = document.getElementById('question');
const pending = document.getElementById('pending');
const success = document.getElementById('success');
const error = document.getElementById('failed');


const serverApiUrl = "http://technical.localhost:8003/api/products"; // CHANGE URL
const intervalTime = 1000;
let interval = null;



window.onload = function () {
    let cookies = document.cookie.split(';');

    for (let cookie of cookies) {
        let [cookieName, cookieValue] = cookie.split('=');

        if (cookieName === 'user') {
            setPendingStyles();
            interval = setInterval(() =>
                    fetch(serverApiUrl+'/is-job-done')
                        .then((res) => res.json())
                        .then((json) => {
                            console.log(json.data.status);
                            if (json.data.status === 'Pending') {
                                return;
                            }

                            if (json.data.status === 'Dispatched') {
                                setSuccessStyles();
                                clearInterval(interval);
                            }

                            if (json.data.status === 'Failed') {
                                setFailedStyles();
                                clearInterval(interval);
                            }
                        })
                , intervalTime);
        }
    }
}

button_submit.addEventListener('click', (e) =>{
    e.preventDefault();
    setPendingStyles();
    fetchData(serverApiUrl);
    interval = setInterval(() =>
        fetch(serverApiUrl+'/is-job-done')
            .then((res) => res.json())
            .then((json) => {
                console.log(json.data.status);
                if (json.data.status === 'Pending') {
                    return;
                }

                if (json.data.status === 'Dispatched') {
                    setSuccessStyles();
                    clearInterval(interval);
                }

                if (json.data.status === 'Failed') {
                    setFailedStyles();
                    clearInterval(interval);
                }
            })
    , intervalTime)
})

button_form_stop.addEventListener('click', (e) =>{
    e.preventDefault();
    setDefaultStyles();
    clearInterval(interval)
})

const setPendingStyles = () => {
    loader.classList.remove("hidden");
    button_form_stop.classList.remove('hidden');
    button_submit.classList.add('hidden');
    question.classList.add('hidden');
    pending.classList.remove('hidden');
}

const setSuccessStyles = () => {
    loader.classList.add("hidden");
    button_form_stop.classList.add('hidden');
    pending.classList.add('hidden');
    success.classList.remove('hidden');
}

const setFailedStyles = () => {
    loader.classList.add("hidden");
    button_form_stop.classList.add('hidden');
    pending.classList.add('hidden');
    error.classList.remove('hidden');
}

const setDefaultStyles = () => {
    loader.classList.add("hidden");
    button_form_stop.classList.add('hidden');
    pending.classList.add('hidden');
    question.classList.remove('hidden');
    button_submit.classList.remove('hidden');
}

const fetchData = (url) =>{
    fetch(url)
        .then((res) => res.json())
        .then((json) => console.log(json));
}
