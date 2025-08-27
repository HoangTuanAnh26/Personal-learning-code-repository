function Validator(options) {
    function getParent(element, selector) {
        while (element.parentElement) {
            if (element.parentElement.matches(selector)) {
                return element.parentElement;
            }
            element = element.parentElement;
        }
        return null;
    }
    var selectorRules = {};
    function validate(inputElement, rule) {
        var _a, _b, _c;
        var errorElement = (_a = getParent(inputElement, options.formGroupSelector)) === null || _a === void 0 ? void 0 : _a.querySelector(options.errorSelector);
        var errorMessage = rule.test(inputElement.value);
        var rules = selectorRules[rule.selector];
        for (var i = 0; i < rules.length; i++) {
            switch (inputElement.type) {
                case 'radio':
                case 'checkbox':
                    errorMessage = rules[i](document.querySelector("".concat(rule.selector, ":checked")));
                    break;
                default:
                    errorMessage = rules[i](inputElement.value);
            }
            if (errorMessage)
                break;
        }
        if (errorMessage) {
            errorElement.innerText = errorMessage;
            (_b = getParent(inputElement, options.formGroupSelector)) === null || _b === void 0 ? void 0 : _b.classList.add('invalid');
        }
        else {
            errorElement.innerText = '';
            (_c = getParent(inputElement, options.formGroupSelector)) === null || _c === void 0 ? void 0 : _c.classList.remove('invalid');
        }
        return !errorMessage;
    }
    var formElement = document.querySelector(options.form);
    if (formElement) {
        formElement.onsubmit = function (e) {
            e.preventDefault();
            var isFormValid = true;
            options.rules.forEach(function (rule) {
                var inputElement = formElement.querySelector(rule.selector);
                var isValid = validate(inputElement, rule);
                if (!isValid) {
                    isFormValid = false;
                }
            });
            if (isFormValid) {
                if (typeof options.onSubmit === 'function') {
                    var enableInputs = formElement.querySelectorAll('[name]:not([disabled])');
                    var formValues = Array.from(enableInputs).reduce(function (values, input) {
                        var _a;
                        switch (input.type) {
                            case 'radio':
                                values[input.name] = (_a = formElement.querySelector("input[name=\"".concat(input.name, "\"]:checked"))) === null || _a === void 0 ? void 0 : _a.value;
                                break;
                            case 'checkbox':
                                if (!input.matches(':checked')) {
                                    values[input.name] = '';
                                    return values;
                                }
                                if (!Array.isArray(values[input.name])) {
                                    values[input.name] = [];
                                }
                                values[input.name].push(input.value);
                                break;
                            case 'file':
                                values[input.name] = input.files;
                                break;
                            default:
                                values[input.name] = input.value;
                        }
                        return values;
                    }, {});
                    options.onSubmit(formValues);
                }
                else {
                    formElement.submit();
                }
            }
        };
        options.rules.forEach(function (rule) {
            if (Array.isArray(selectorRules[rule.selector])) {
                selectorRules[rule.selector].push(rule.test);
            }
            else {
                selectorRules[rule.selector] = [rule.test];
            }
            var inputElements = formElement.querySelectorAll(rule.selector);
            Array.from(inputElements).forEach(function (inputElement) {
                inputElement.onblur = function () {
                    validate(inputElement, rule);
                };
                inputElement.oninput = function () {
                    var _a, _b;
                    var errorElement = (_a = getParent(inputElement, options.formGroupSelector)) === null || _a === void 0 ? void 0 : _a.querySelector(options.errorSelector);
                    errorElement.innerText = '';
                    (_b = getParent(inputElement, options.formGroupSelector)) === null || _b === void 0 ? void 0 : _b.classList.remove('invalid');
                };
            });
        });
    }
}
Validator.isRequired = function (selector, message) {
    return {
        selector: selector,
        test: function (value) {
            return value ? undefined : message || 'Vui lòng nhập trường này';
        },
    };
};
Validator.isEmail = function (selector, message) {
    return {
        selector: selector,
        test: function (value) {
            var regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return regex.test(value) ? undefined : message || 'Trường này phải là email';
        },
    };
};
Validator.minLength = function (selector, min, message) {
    return {
        selector: selector,
        test: function (value) {
            return value.length >= min ? undefined : message || "Vui l\u00F2ng nh\u1EADp t\u1ED1i thi\u1EC3u ".concat(min, " k\u00FD t\u1EF1");
        },
    };
};
Validator.isConfirmed = function (selector, getConfirmValue, message) {
    return {
        selector: selector,
        test: function (value) {
            return value === getConfirmValue() ? undefined : message || 'Giá trị nhập vào không chính xác';
        },
    };
};
