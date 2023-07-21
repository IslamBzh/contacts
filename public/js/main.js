document.addEventListener("DOMContentLoaded", function(){
    for (const form of document.getElementsByTagName('form'))
        form.onsubmit = formSubmit

    for (const phone_number of document.querySelectorAll('input[type="phone_number"]')){
        phone_number.setAttribute('type', 'text');
        phone_number.setAttribute('invalid', '');

        phoneMask = IMask(phone_number, {
            mask: [
                {
                    mask: '{8} 000 000 00 00'
                }, {
                    mask: '+{7} 000 000 00 00'
                }
            ]
        });

        phone_number.addEventListener("input", function(){

            if (phoneMask.masked.isComplete)
                phone_number.removeAttribute('invalid');

            else
                phone_number.setAttribute('invalid', '');
        });
    }
});

async function formSubmit(e){
    e.preventDefault();

    invalids = this.querySelectorAll('input[invalid]');
    if(invalids.length > 0){
        invalids[0].focus();
        return false;
    }

    var action = this.getAttribute('action');

    var data = new FormData(this);

    response = await fetchRequest(action, data);

    if(response.status)
        Contacts.add(response.result)

    for (const input of this.querySelectorAll('input:not([type="hidden"]):not([type="submit"])'))
        input.value = '';

    return false;
}

async function fetchRequest(url, data, callback_fn = null){
    let response = await fetch(url, {
        headers: {
            'X-CSRF-Token': CSRF
        },
        method: 'POST',
        body: data
    });

    let response_json = await response.json();

    if (typeof callback_fn === 'function')
        callback_fn(response_json);

    return response_json;
}