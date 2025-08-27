interface ValidatorOptions {
    form: string;
    formGroupSelector: string;
    errorSelector: string;
    rules: ValidatorRule[];
    onSubmit?: (data: any) => void;
  }
  
  interface ValidatorRule {
    selector: string;
    test: (value: string) => string | undefined;
  }
  
  function Validator(options: ValidatorOptions) {
    function getParent(element: HTMLElement, selector: string): HTMLElement | null {
      while (element.parentElement) {
        if (element.parentElement.matches(selector)) {
          return element.parentElement;
        }
        element = element.parentElement;
      }
      return null;
    }
  
    const selectorRules: { [key: string]: Function[] } = {};
  
    function validate(inputElement: HTMLInputElement, rule: ValidatorRule): boolean {
      const errorElement = getParent(inputElement, options.formGroupSelector)?.querySelector(options.errorSelector) as HTMLElement;
      const errorMessage = rule.test(inputElement.value);
  
      let rules = selectorRules[rule.selector];
      for (let i = 0; i < rules.length; i++) {
        switch (inputElement.type) {
          case 'radio':
          case 'checkbox':
            errorMessage = rules[i](document.querySelector(`${rule.selector}:checked`) as HTMLInputElement);
            break;
          default:
            errorMessage = rules[i](inputElement.value);
        }
  
        if (errorMessage) break;
      }
  
      if (errorMessage) {
        errorElement.innerText = errorMessage;
        getParent(inputElement, options.formGroupSelector)?.classList.add('invalid');
      } else {
        errorElement.innerText = '';
        getParent(inputElement, options.formGroupSelector)?.classList.remove('invalid');
      }
  
      return !errorMessage;
    }
  
    const formElement = document.querySelector(options.form) as HTMLFormElement;
  
    if (formElement) {
      formElement.onsubmit = function (e: Event) {
        e.preventDefault();
  
        let isFormValid = true;
  
        options.rules.forEach((rule) => {
          const inputElement = formElement.querySelector(rule.selector) as HTMLInputElement;
          const isValid = validate(inputElement, rule);
          if (!isValid) {
            isFormValid = false;
          }
        });
  
        if (isFormValid) {
          if (typeof options.onSubmit === 'function') {
            const enableInputs = formElement.querySelectorAll('[name]:not([disabled])');
            const formValues = Array.from(enableInputs).reduce((values: any, input: HTMLInputElement) => {
              switch (input.type) {
                case 'radio':
                  values[input.name] = formElement.querySelector(`input[name="${input.name}"]:checked`)?.value;
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
          } else {
            formElement.submit();
          }
        }
      };
  
      options.rules.forEach((rule) => {
        if (Array.isArray(selectorRules[rule.selector])) {
          selectorRules[rule.selector].push(rule.test);
        } else {
          selectorRules[rule.selector] = [rule.test];
        }
  
        const inputElements = formElement.querySelectorAll(rule.selector);
  
        Array.from(inputElements).forEach((inputElement) => {
          inputElement.onblur = function () {
            validate(inputElement as HTMLInputElement, rule);
          };
  
          inputElement.oninput = function () {
            const errorElement = getParent(inputElement as HTMLElement, options.formGroupSelector)?.querySelector(options.errorSelector) as HTMLElement;
            errorElement.innerText = '';
            getParent(inputElement as HTMLElement, options.formGroupSelector)?.classList.remove('invalid');
          };
        });
      });
    }
  }
  
  Validator.isRequired = function (selector: string, message: string) {
    return {
      selector: selector,
      test: function (value: string) {
        return value ? undefined : message || 'Vui lòng nhập trường này';
      },
    };
  };
  
  Validator.isEmail = function (selector: string, message: string) {
    return {
      selector: selector,
      test: function (value: string) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(value) ? undefined : message || 'Trường này phải là email';
      },
    };
  };
  
  Validator.minLength = function (selector: string, min: number, message: string) {
    return {
      selector: selector,
      test: function (value: string) {
        return value.length >= min ? undefined : message || `Vui lòng nhập tối thiểu ${min} ký tự`;
      },
    };
  };
  
  Validator.isConfirmed = function (selector: string, getConfirmValue: () => string, message: string) {
    return {
      selector: selector,
      test: function (value: string) {
        return value === getConfirmValue() ? undefined : message || 'Giá trị nhập vào không chính xác';
      },
    };
  };
  