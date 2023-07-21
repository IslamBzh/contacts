const Contacts = {
    container:      document.getElementById('contacts'),
    contacts_count: document.getElementById('contacts_count'),

    add: function(contact){
        var block = document.createElement('div');
        block.classList.add('contact');

        var info_block = document.createElement('div');
        info_block.classList.add('contact__info');
        block.appendChild(info_block);

        var span_name = document.createElement('span');
        span_name.classList.add('contact__info');
        span_name.innerHTML = contact.name;
        info_block.appendChild(span_name);

        var del_act = document.createElement('a');
        del_act.classList.add('contact__delete');
        del_act.href = '//';
        info_block.appendChild(del_act);

        var phone = document.createElement('a');
        phone.classList.add('contact__phone_number');
        phone.href = 'tel:' + contact.phone_number;
        phone.innerHTML = this.phone_format(contact.phone_number);
        info_block.appendChild(phone);
        block.appendChild(phone);

        del_act.addEventListener("click", function(e){
            e.preventDefault();

            Contacts.rem(contact.id, block);
            return false;
        });

        this.container.appendChild(block);

        this.upd_contacts_count();
    },

    set: function(contacts){
        for (var i = 0; i < contacts.length; i++)
            this.add(contacts[i]);
    },

    rem: function(contact_id, block){
        var data = new FormData();
        data.append('contact_id', contact_id);

        fetchRequest('/contacts/api/delete', data, function(response){
            if(response.status)
                block.remove();
        });

        this.upd_contacts_count();
    },

    upd_contacts_count: function (){

        contacts_count.innerHTML = this.container.querySelectorAll('.contact').length;
    },

    phone_format: function (n){
        n = '' + n;

        if(n.substring(0, 1) == '7')
            return '+' + n.substring(0, 1) + ' ' + n.substring(1, 4) + ' ' + n.substring(4, 7) + ' ' + n.substring(7, 9) + ' ' + n.substring(9, 12);

        return n.substring(0, 1) + ' ' + n.substring(1, 4) + ' ' + n.substring(4, 7) + ' ' + n.substring(7, 9) + ' ' + n.substring(9, 12);
    }
};

fetchRequest('/contacts/api/getAll', {}, function(response){
    if(response.status)
        Contacts.set(response.result);
});