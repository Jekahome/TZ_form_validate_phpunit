"use strict";

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _objectWithoutProperties(obj, keys) { var target = {}; for (var i in obj) { if (keys.indexOf(i) >= 0) continue; if (!Object.prototype.hasOwnProperty.call(obj, i)) continue; target[i] = obj[i]; } return target; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var User = function () {
    function User(_ref) {
        var form = _ref.form,
            save = _ref.save,
            params = _objectWithoutProperties(_ref, ['form', 'save']);

        _classCallCheck(this, User);

        this._form = form;
        this._save = save;
        this.isValid = true;
        this._names = Array.prototype.map.call(this._form.elements, function (el) {
            return el.name;
        });

        this.eventInput = this.eventInput.bind(this);
        this.validatorInput = this.validatorInput.bind(this);

        this.init();
    }

    _createClass(User, [{
        key: 'init',
        value: function init() {
            var _this = this;

            for (var i = 0; i < this._form.length; i++) {
                if (this._form.elements[i] != this._form.email) this._form.elements[i].addEventListener('input', this.eventInput, false);
            }

            this._form.email.addEventListener('change', this.eventInput, false);
            this._form.password.addEventListener('copy', function (event) {
                event.preventDefault();
            }, false);
            this._form.confirmPassword.addEventListener('paste', function (event) {
                event.preventDefault();
            }, false);

            // => this = class or bind(this) or variables = this;
            this._form.password.addEventListener('change', function (event) {

                if (_this._form.password.checkValidity()) {
                    _this._form.confirmPassword.disabled = false;
                } else {
                    _this._form.confirmPassword.disabled = true;
                }
            }, false);

            this._save.addEventListener('click', function (event) {

                _this.isValid = true;
                _this.removeErroAllMessages();

                if (_this._form.checkValidity() == false) {

                    for (var i = 0; i < _this._form.length; i++) {

                        if (_this._form.hasOwnProperty(i) && _this._form.elements[i].getAttribute('type') != 'submit' && _this._form.elements[i].checkValidity() == false) {

                            var errors = _this.validatorInput(_this._form.elements[i]);

                            if (errors.length > 0) {
                                _this.isValid = false;

                                // form.elements[i].setCustomValidity(errors);// для всплывающих окон
                                _this.addErrorMessages(_this._form.elements[i], errors);
                            }
                        } else {
                            // form.elements[i].setCustomValidity("");//снять ошибку
                        }
                    }

                    if (_this.isValid == false) {
                        event.preventDefault();
                        event.stopPropagation();
                        return false;
                    }
                }

                var errors = _this.validatorInput(_this._form.confirmPassword);
                if (errors.length > 0) {
                    _this.addErrorMessages(_this._form.confirmPassword, errors);
                    event.preventDefault();
                    event.stopPropagation();
                    return false;
                }

                if (_this.isValid) {
                    _this.submit();
                }
            });
        }
    }, {
        key: 'validatorInput',
        value: function validatorInput(input) {
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
    }, {
        key: 'eventInput',
        value: function eventInput(event) {
            var el = event.currentTarget;

            if (this._names.indexOf(el.name, 0) != -1) {

                this.removeErrorMessages(el);

                if (el.name == 'confirmPassword') {

                    var errors = this.validatorInput(el);
                    if (errors.length > 0) this.addErrorMessages(el, errors);
                } else {
                    if (el.checkValidity() == false) {
                        var errors = this.validatorInput(el);
                        if (errors.length > 0) this.addErrorMessages(el, errors);
                    }
                }
            }
        }
    }, {
        key: 'removeErrorMessages',
        value: function removeErrorMessages(el) {
            var errorsMess = el.closest('div').querySelectorAll("small.form-text");
            for (var i = 0; i < errorsMess.length; i++) {
                if (errorsMess[i] && el.closest('div').contains(errorsMess[i])) el.closest('div').removeChild(errorsMess[i]);
            }
        }
    }, {
        key: 'removeErroAllMessages',
        value: function removeErroAllMessages() {
            var errorsMess = this._form.querySelectorAll("small.form-text");
            for (var i = 0; i < errorsMess.length; i++) {
                if (errorsMess[i]) errorsMess[i].closest('div').removeChild(errorsMess[i]);
            }
        }
    }, {
        key: 'addErrorMessages',
        value: function addErrorMessages(el, errors) {
            el.insertAdjacentHTML('afterend', '<small class="form-text text-muted"> ' + errors + '</small>');
            this.isValid = false;
        }
    }, {
        key: 'submit',
        value: function submit() {
            event.preventDefault();
            var This = this;
            //var formData = new FormData();
            //if (this._form.elements.file.files) { formData.append('files', this._form.elements.file.files[0]);}

            var data = $(this._form).serialize();
            $.ajax({
                type: 'PUT', // PUT не работает с FormData (enctype="multipart/form-data")
                url: this._form.action,
                contentType: 'application/json',
                cache: false,
                processData: false,
                data: data,
                success: function success(data) {
                    try {
                        if (data) {
                            data = JSON.parse(data);
                            if (data.errors) {
                                for (var name in data.errors) {
                                    for (var k in data.errors[name]) {
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
    }]);

    return User;
}();

var UserFile = function () {
    function UserFile(_ref2) {
        var form = _ref2.form,
            save = _ref2.save,
            params = _objectWithoutProperties(_ref2, ['form', 'save']);

        _classCallCheck(this, UserFile);

        this._form = form;
        this._save = save;
        this.isValid = true;
        this._buffer = null;
        this._names = Array.prototype.map.call(this._form.elements, function (el) {
            return el.name;
        });
        this.validatorFile = this.validatorFile.bind(this);
        this.init();
    }

    _createClass(UserFile, [{
        key: 'init',
        value: function init() {
            var _this2 = this;

            this._save.addEventListener('click', function (event) {
                _this2.isValid = true;
                _this2.removeErroAllMessages();
                var errors = _this2.validatorFile(_this2._form.file);
                if (errors.length > 0) {
                    _this2.addErrorMessages(_this2._form.file, errors);
                    event.preventDefault();
                    event.stopPropagation();
                    return false;
                }
                if (_this2.isValid) {
                    _this2.submit();
                }
            });
        }
    }, {
        key: 'validatorFile',
        value: function validatorFile(input) {
            var messages = [];
            var ext2 = ['image/jpeg' /*, 'image/png', 'image/gif', 'image/pjpeg'*/];
            if (input.type == 'file' && input.value) {
                var file = input.files[0];
                if (file.size > 200000) {
                    messages.push(translit("maximum weight", (file.size / 1000000).toFixed(1)));
                }
                if (ext2.indexOf(file.type, 0) == -1) {
                    messages.push(translit("mime types", ext2.join(' ,')));
                }
            }
            return messages.join(". <br/>");
        }
    }, {
        key: 'removeErrorMessages',
        value: function removeErrorMessages(el) {
            var errorsMess = el.closest('div').querySelectorAll("small.form-text");
            for (var i = 0; i < errorsMess.length; i++) {
                if (errorsMess[i] && el.closest('div').contains(errorsMess[i])) el.closest('div').removeChild(errorsMess[i]);
            }
        }
    }, {
        key: 'removeErroAllMessages',
        value: function removeErroAllMessages() {
            var errorsMess = this._form.querySelectorAll("small.form-text");
            for (var i = 0; i < errorsMess.length; i++) {
                if (errorsMess[i]) errorsMess[i].closest('div').removeChild(errorsMess[i]);
            }
        }
    }, {
        key: 'addErrorMessages',
        value: function addErrorMessages(el, errors) {
            el.insertAdjacentHTML('afterend', '<small class="form-text text-muted"> ' + errors + '</small>');
            this.isValid = false;
        }
    }, {
        key: 'submit',
        value: function submit() {
            event.preventDefault();
            var This = this;
            var formData = new FormData();
            formData.append('files', this._form.elements.file.files[0]);

            $.ajax({
                type: 'POST', // PUT не работает с FormData (enctype="multipart/form-data")
                url: this._form.action,
                contentType: false,
                cache: false,
                processData: false,
                data: formData,
                success: function success(data) {
                    try {
                        if (data) {

                            data = JSON.parse(data);
                            if (data.errors) {
                                for (var name in data.errors) {
                                    for (var k in data.errors[name]) {
                                        This.addErrorMessages(This._form.elements[name], data.errors[name][k]);
                                    }
                                }
                            } else if (data.messages) {
                                //confirm(data.messages);
                                $('#userModal .modal-body').html("<div class='alert alert-warning'>" + data.messages + "</div>");
                                $('#userModal').modal('show');

                                window.setTimeout(function () {
                                    location.reload();
                                }, 3000);
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
    }]);

    return UserFile;
}();

window.onload = function () {
    new User({ form: document.forms.userForm, save: document.getElementById('save') });
    new UserFile({ form: document.forms.userFormFile, save: document.getElementById('saveFile') });
};