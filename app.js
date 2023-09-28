// Fetch bio data
hfPostRequest('ajax.php?action=bio').then(response => {
    if ( response.status == 200 ) {
        let bio = response.content;
        _e('#bio-name').innerHTML = bio.name;
        _e('#bio-email').innerHTML = bio.email;
        _e('#bio-phone').innerHTML = bio.phone;
        _e('#bio-dob').innerHTML = bio.dob_n_age;
        _e('#bio-website').innerHTML = bio.website_link;
        _e('#last-updated').innerHTML = `Last updated on ${bio.last_updated_date_formatted}`;

        let l = '<ul class="lang-list">';
        for ( let i = 0; i < bio.languages.length; i++ ) { l += `<li>${bio.languages[i]}</li>`; }
        l += `</ul>`;
        _e('#languages').innerHTML = l;

    } else if ( response.status == 404 ) {
        showNotification( response.message, 'warning' );
    } else {
        showNotification( response.message, 'error' );
    }
});

// Fetch all the work experiences
hfPostRequest('ajax.php?action=work-experience').then(response => {
    if ( response.status == 200 ) {
        _e('#work-experiences').innerHTML = response.content;
        let mibtns = document.querySelectorAll('.more-info-btn');
        if ( mibtns.length > 0 ) {
            for ( let i = 0; i < mibtns.length; i++ ) {
                mibtns[i].addEventListener('click', function(e){

                    e.preventDefault();

                    let btn = mibtns[i];
                    let defaultText = btn.innerHTML;

                    let targetId = btn.dataset.targetId;

                    btn.innerHTML = `<i class="fa-solid fa-spin fa-spinner"></i> Loading...`;

                    let payload = new FormData();
                    payload.append("pages", btn.dataset.pages);

                    hfPostRequest('ajax.php?action=get-experiences', payload).then(response => {

                        btn.innerHTML = defaultText;

                        if ( response.status == 200 ) {
                            btn.style.display = 'none';
                            let weBox = _e(`#we-box-${targetId}`);
                            weBox.innerHTML = response.content;
                            weBox.style.display = 'block';
                        } else if ( response.status == 404 ) {
                            showNotification( response.message, 'warning' );
                        } else {
                            showNotification( response.message, 'error' );
                        }

                    });

                    // mibtns[i].style.display = 'none';
                    // let t = e.target.dataset.target;
                    // _e(`#${t}`).style.display = 'block';
                });
            }
        }

    } else if ( response.status == 404 ) {
        showNotification( response.message, 'warning' );
    } else {
        showNotification( response.message, 'error' );
    }
});

// Fetch all Educational Qualification
hfPostRequest('ajax.php?action=education-qualification').then(response => {
    if ( response.status == 200 ) {
        _e('#education').innerHTML = response.content;
    } else if ( response.status == 404 ) {
        showNotification( response.message, 'warning' );
    } else {
        showNotification( response.message, 'error' );
    }
});

// Skills
hfPostRequest('ajax.php?action=skills').then(response => {
    if ( response.status == 200 ) {
        _e('#skills').innerHTML = response.content;
    } else if ( response.status == 404 ) {
        showNotification( response.message, 'warning' );
    } else {
        showNotification( response.message, 'error' );
    }
});

// Skills
hfPostRequest('ajax.php?action=projects').then(response => {
    if ( response.status == 200 ) {
        _e('#projects').innerHTML = response.content;
    } else if ( response.status == 404 ) {
        showNotification( response.message, 'warning' );
    } else {
        showNotification( response.message, 'error' );
    }
});

function _e(element) {
    return document.querySelector(element);
}

function showNotification( msg, type ) {
    let nBox = _e('#notification');
    nBox.innerHTML = msg;
    nBox.classList.add('reveal-notify');
    if ( type == "success" ) {
        nBox.classList.remove('notification-warning');
        nBox.classList.remove('notification-error');
        nBox.classList.add('notification-success');
    } else if ( type == 'warning' ) {
        nBox.classList.remove('notification-error');
        nBox.classList.remove('notification-success');
        nBox.classList.add('notification-warning');
    } else {
        nBox.classList.remove('notification-success');
        nBox.classList.remove('notification-warning');
        nBox.classList.add('notification-error');
    }
    setInterval(function() {
        nBox.classList.remove('reveal-notify');
    }, 5000);
}

async function hfPostRequest(url, dataset = {}) {
    const response = await fetch(url, {
        method: "POST",
        mode: "same-origin",
        cache: "no-cache",
        credentials: "same-origin",
        redirect: "follow",
        referrerPolicy: "no-referrer",
        headers: {
            "Tken" : _e('#xtoken').content
        },
        body: dataset
    });
    const json = await response.json();
    return json;
}