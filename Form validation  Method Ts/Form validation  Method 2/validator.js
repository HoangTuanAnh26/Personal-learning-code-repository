var Validator = /** @class */ (function () {
    function Validator(formSelector) {
        this.onSubmit = function (data) { return console.log(data); };
        this.validatorRules = {
            required: function (value) { return (value ? undefined : "Vui lòng nhập trường này"); },
            email: function (value) {
                var regex = /^\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,3}$/;
                return regex.test(value) ? undefined : "Vui lòng nhập email hợp lệ";
            },
            min: function (min) {
                return function (value) {
                    return value.length >= min ? undefined : "Vui l\u00F2ng nh\u1EADp \u00EDt nh\u1EA5t ".concat(min, " k\u00FD t\u1EF1");
                };
            },
            max: function (max) {
                return function (value) {
                    return value.length <= max ? undefined : "Vui l\u00F2ng nh\u1EADp nhi\u1EC1u nh\u1EA5t ".concat(max, " k\u00FD t\u1EF1");
                };
            },
        };
        this.formElement = document.querySelector(formSelector);
        this.formRules = {};
        if (this.formElement) {
            this.initialize();
        }
    }
    Validator.prototype.getParent = function (element, selector) {
        var parent = element.parentElement;
        while (parent) {
            if (parent.matches(selector)) {
                return parent;
            }
            parent = parent.parentElement;
        }
        return null;
    };
    Validator.prototype.initialize = function () {
        var _a;
        var inputs = this.formElement.querySelectorAll("[name][rules]");
        for (var _i = 0, inputs_1 = inputs; _i < inputs_1.length; _i++) {
            var input = inputs_1[_i];
            var rules = ((_a = input.getAttribute("rules")) === null || _a === void 0 ? void 0 : _a.split("|")) || [];
            for (var _b = 0, rules_1 = rules; _b < rules_1.length; _b++) {
                var rule = rules_1[_b];
                var ruleFunc = void 0;
                var isRuleHasValue = rule.includes(":");
                if (isRuleHasValue) {
                    var _c = rule.split(":"), ruleName = _c[0], ruleValue = _c[1];
                    rule = ruleName;
                    ruleFunc = this.validatorRules[rule](parseInt(ruleValue));
                }
                else {
                    ruleFunc = this.validatorRules[rule];
                }
                if (Array.isArray(this.formRules[input.name])) {
                    this.formRules[input.name].push(ruleFunc);
                }
                else {
                    this.formRules[input.name] = [ruleFunc];
                }
            }
            input.onblur = this.handleValidate.bind(this);
            input.oninput = this.handleClearError.bind(this);
        }
        this.formElement.onsubmit = this.handleSubmit.bind(this);
    };
    Validator.prototype.handleValidate = function (event) {
        var input = event.target;
        var rules = this.formRules[input.name];
        var errorMessage;
        for (var _i = 0, rules_2 = rules; _i < rules_2.length; _i++) {
            var rule = rules_2[_i];
            errorMessage = rule(input.value);
            if (errorMessage)
                break;
        }
        if (errorMessage) {
            var formGroup = this.getParent(input, ".form-group");
            if (formGroup) {
                formGroup.classList.add("invalid");
                var formMessage = formGroup.querySelector(".form-message");
                if (formMessage) {
                    formMessage.innerText = errorMessage;
                }
            }
        }
        return !errorMessage;
    };
    Validator.prototype.handleClearError = function (event) {
        var input = event.target;
        var formGroup = this.getParent(input, ".form-group");
        if (formGroup === null || formGroup === void 0 ? void 0 : formGroup.classList.contains("invalid")) {
            formGroup.classList.remove("invalid");
            var formMessage = formGroup.querySelector(".form-message");
            if (formMessage) {
                formMessage.innerText = "";
            }
        }
    };
    Validator.prototype.handleSubmit = function (event) {
        var _this = this;
        event.preventDefault();
        var inputs = this.formElement.querySelectorAll("[name][rules]");
        var isValid = true;
        for (var _i = 0, inputs_2 = inputs; _i < inputs_2.length; _i++) {
            var input = inputs_2[_i];
            if (!this.handleValidate({ target: input })) {
                isValid = false;
            }
        }
        if (isValid) {
            var enableInputs = this.formElement.querySelectorAll("[name]:not([disabled])");
            var formValues = Array.from(enableInputs).reduce(function (values, input) {
                switch (input.type) {
                    case "radio":
                        var checkedRadio = _this.formElement.querySelector("input[name=\"".concat(input.name, "\"]:checked"));
                        values[input.name] = (checkedRadio === null || checkedRadio === void 0 ? void 0 : checkedRadio.value) || "";
                        break;
                    case "checkbox":
                        if (!input.matches(":checked")) {
                            values[input.name] = "";
                            break;
                        }
                        if (!Array.isArray(values[input.name])) {
                            values[input.name] = [];
                        }
                        values[input.name].push(input.value);
                        break;
                    case "file":
                        values[input.name] = input.files;
                        break;
                    default:
                        values[input.name] = input.value;
                }
                return values;
            }, {});
            if (typeof this.onSubmit === "function") {
                this.onSubmit(formValues);
            }
            else {
                this.formElement.submit();
            }
        }
    };
    return Validator;
}());
// Khởi tạo Validator
var form = new Validator("#register-form");
form.onSubmit = function (formData) {
    console.log(formData);
};
