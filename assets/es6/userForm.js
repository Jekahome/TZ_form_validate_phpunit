"use strict";

class User {
    constructor({form, save, ...params}) {

        this._form = form;
        this._save = save;
        this.isValid = true;
        this._names = Array.prototype.map.call(this._form.elements, function (el) {
            return el.name
        });

        this.eventInput = this.eventInput.bind(this);
        this.validatorInput = this.validatorInput.bind(this);

        this.init();
    }

    init() {

        for (var i = 0; i < this._form.length; i++) {
            if (this._form.elements[i] != this._form.email)
                this._form.elements[i].addEventListener('input', this.eventInput, false);
        }

        this._form.email.addEventListener('change', this.eventInput, false);
        this._form.password.addEventListener('copy', function (event) {
            event.preventDefault()
        }, false);
        this._form.confirmPassword.addEventListener('paste', function (event) {
            event.preventDefault()
        }, false);

        // => this = class or bind(this) or variables = this;
        this._form.password.addEventListener('change', (event) => {

            if (this._form.password.checkValidity()) {
                this._form.confirmPassword.disabled = false;
            } else {
                this._form.confirmPassword.disabled = true;
            }
        }, false);

        this._save.addEventListener('click', (event) => {

            this.isValid = true;
            this.removeErroAllMessages();

            if (this._form.checkValidity() == false) {

                for (var i = 0; i < this._form.length; i++) {

                    if (this._form.hasOwnProperty(i) &&
                        this._form.elements[i].getAttribute('type') != 'submit' &&
                        this._form.elements[i].checkValidity() == false
                    ) {

                        var errors = this.validatorInput(this._form.elements[i]);

                        if (errors.length > 0) {
                            this.isValid = false;

                            // form.elements[i].setCustomValidity(errors);// для всплывающих окон
                            this.addErrorMessages(this._form.elements[i], errors);
                        }

                    } else {
                        // form.elements[i].setCustomValidity("");//снять ошибку
                    }
                }

                if (this.isValid == false) {
                    event.preventDefault();
                    event.stopPropagation();
                    return false;
                }
            }

            var errors = this.validatorInput(this._form.confirmPassword);
            if (errors.length > 0) {
                this.addErrorMessages(this._form.confirmPassword, errors);
                event.preventDefault();
                event.stopPropagation();
                return false;
            }


            if (this.isValid) {
                this.submit();
            }
        });
    }

    validatorInput(input) {
        var validity = input.validity;
        var messages = [];

        if (validity.patternMismatch) {
            messages.push(input.title);
        }

        if (validity.typeMismatch) {
            messages.push(input.title);
        }

        if (input.name == 'confirmPassword') {
            if (this._form.password.value != input.value) {
                messages.push(translit("password-confirm"));
            }
        }

        if (!messages.length && validity.valueMissing) {
            messages.push(translit("required"));
        }

        return messages.join(".  <br/>");
    }

    eventInput(event) {
        let el = event.currentTarget;

        if (this._names.indexOf(el.name, 0) != -1) {

            this.removeErrorMessages(el);

             if (el.name == 'confirmPassword') {

                var errors = this.validatorInput(el);
                if (errors.length > 0) this.addErrorMessages(el, errors);
            }
            else {
                if (el.checkValidity() == false) {
                    var errors = this.validatorInput(el);
                    if (errors.length > 0) this.addErrorMessages(el, errors);
                }
            }
        }
    }

    removeErrorMessages(el) {
        var errorsMess = el.closest('div').querySelectorAll("small.form-text");
        for (var i = 0; i < errorsMess.length; i++)
            if (errorsMess[i] && el.closest('div').contains(errorsMess[i]))
                el.closest('div').removeChild(errorsMess[i]);
    }

    removeErroAllMessages() {
        var errorsMess = this._form.querySelectorAll("small.form-text");
        for (var i = 0; i < errorsMess.length; i++)
            if (errorsMess[i])
                errorsMess[i].closest('div').removeChild(errorsMess[i]);
    }

    addErrorMessages(el, errors) {
        el.insertAdjacentHTML(
            'afterend',
            '<small class="form-text text-muted"> ' + errors + '</small>');
        this.isValid = false;
    }

    submit() {
        event.preventDefault();
        var This = this;
        //var formData = new FormData();
        //if (this._form.elements.file.files) { formData.append('files', this._form.elements.file.files[0]);}

        var data = $(this._form).serialize();
        $.ajax({
            type: 'PUT',// PUT не работает с FormData (enctype="multipart/form-data")
            url: this._form.action,
            contentType: 'application/json',
            cache: false,
            processData: false,
            data: data,
            success: function (data) {
                try {
                    if (data) {
                        data = JSON.parse(data);
                        if (data.errors) {
                            for (var name in data.errors) {
                                for(var k in data.errors[name]){
                                    This.addErrorMessages(This._form.elements[name], data.errors[name][k]);
                                }
                            }
                        } else if (data.messages) {
                            //confirm(data.messages);
                            $('#registrationModal .modal-body').html("<div class='alert alert-warning'>" + data.messages + "</div>");
                            $('#registrationModal').modal('show');

                            //window.setTimeout(function(){  window.location.href = '/';},3000);
                        }
                    }
                } catch (e) {
                    console.log('error', e);
                }
            }
        });
    }
}


class UserFile {
    constructor({form, save, ...params}) {
        this._form = form;
        this._save = save;
        this.isValid = true;
        this._buffer = null;
        this._names = Array.prototype.map.call(this._form.elements, function (el) {
            return el.name
        });
        this.validatorFile = this.validatorFile.bind(this);
        this.init();
    }

    init() {
        this._save.addEventListener('click', (event) => {
            this.isValid = true;
            this.removeErroAllMessages();
            var errors = this.validatorFile(this._form.file);
            if (errors.length > 0) {
                this.addErrorMessages(this._form.file, errors);
                event.preventDefault();
                event.stopPropagation();
                return false;
            }
            if (this.isValid) {
                this.submit();
            }
        });
    }

    validatorFile(input) {
        var messages = [];
        var ext2 = ['image/jpeg'/*, 'image/png', 'image/gif', 'image/pjpeg'*/];
        if (input.type == 'file' && input.value) {
            var file = input.files[0];
            if (file.size > 200000) {
                messages.push(translit("maximum weight", ((file.size / 1000000).toFixed(1))));
            }
            if (ext2.indexOf(file.type, 0) == -1) {
                messages.push(translit("mime types", ext2.join(' ,')));
            }
        }
        return messages.join(". <br/>");
    }
    
    removeErrorMessages(el) {
        var errorsMess = el.closest('div').querySelectorAll("small.form-text");
        for (var i = 0; i < errorsMess.length; i++)
            if (errorsMess[i] && el.closest('div').contains(errorsMess[i]))
                el.closest('div').removeChild(errorsMess[i]);
    }

    removeErroAllMessages() {
        var errorsMess = this._form.querySelectorAll("small.form-text");
        for (var i = 0; i < errorsMess.length; i++)
            if (errorsMess[i])
                errorsMess[i].closest('div').removeChild(errorsMess[i]);
    }

    addErrorMessages(el, errors) {
        el.insertAdjacentHTML(
            'afterend',
            '<small class="form-text text-muted"> ' + errors + '</small>');
        this.isValid = false;
    }

    submit() {
        event.preventDefault();
        var This = this;
        var formData = new FormData();
        formData.append('files', this._form.elements.file.files[0]);

        $.ajax({
            type: 'POST',// PUT не работает с FormData (enctype="multipart/form-data")
            url: this._form.action,
           contentType: false,
            cache: false,
            processData: false,
            data: formData,
            success: function (data) {
                try {
                    if (data) {

                        data = JSON.parse(data);
                        if (data.errors) {
                            for (var name in data.errors) {
                                for(var k in data.errors[name]){
                                    This.addErrorMessages(This._form.elements[name], data.errors[name][k]);
                                }
                            }
                        } else if (data.messages) {
                            //confirm(data.messages);
                            $('#userModal .modal-body').html("<div class='alert alert-warning'>" + data.messages + "</div>");
                            $('#userModal').modal('show');

                             window.setTimeout(function(){  location.reload();},3000);
                        } else {
                            window.location.href = '/';
                        }
                    }
                } catch (e) {
                    console.log('error', e);
                }
            }
        });
    }
}
window.onload = function () {
    new User({form: document.forms.userForm, save: document.getElementById('save')});
    new UserFile({form: document.forms.userFormFile, save: document.getElementById('saveFile')});
};