// Định nghĩa interface cho dữ liệu form
interface FormValues {
    [key: string]: string | string[] | FileList | null;
  }
  
  // Định nghĩa kiểu cho hàm validation
  type ValidationFunction = (value: string) => string | undefined;
  
  // Định nghĩa kiểu cho validator rules
  interface ValidatorRules {
    required: ValidationFunction;
    email: ValidationFunction;
    min: (min: number) => ValidationFunction;
    max: (max: number) => ValidationFunction;
  }
  
  class Validator {
    private formElement: HTMLFormElement;
    private formRules: { [key: string]: ValidationFunction[] };
    public onSubmit: (data: FormValues) => void = (data) => console.log(data);
  
    constructor(formSelector: string) {
      this.formElement = document.querySelector(formSelector) as HTMLFormElement;
      this.formRules = {};
  
      if (this.formElement) {
        this.initialize();
      }
    }
  
    private getParent(element: HTMLElement, selector: string): HTMLElement | null {
      let parent = element.parentElement;
      while (parent) {
        if (parent.matches(selector)) {
          return parent;
        }
        parent = parent.parentElement;
      }
      return null;
    }
  
    private validatorRules: ValidatorRules = {
      required: (value: string) => (value ? undefined : "Vui lòng nhập trường này"),
      email: (value: string) => {
        const regex = /^\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,3}$/;
        return regex.test(value) ? undefined : "Vui lòng nhập email hợp lệ";
      },
      min: (min: number) => (value: string) =>
        value.length >= min ? undefined : `Vui lòng nhập ít nhất ${min} ký tự`,
      max: (max: number) => (value: string) =>
        value.length <= max ? undefined : `Vui lòng nhập nhiều nhất ${max} ký tự`,
    };
  
    private initialize(): void {
      const inputs = this.formElement.querySelectorAll<HTMLInputElement>("[name][rules]");
      for (const input of inputs) {
        const rules = input.getAttribute("rules")?.split("|") || [];
        for (let rule of rules) {
          let ruleFunc: ValidationFunction;
          const isRuleHasValue = rule.includes(":");
  
          if (isRuleHasValue) {
            const [ruleName, ruleValue] = rule.split(":");
            rule = ruleName;
            ruleFunc = this.validatorRules[rule as keyof ValidatorRules](parseInt(ruleValue));
          } else {
            ruleFunc = this.validatorRules[rule as keyof ValidatorRules];
          }
  
          if (Array.isArray(this.formRules[input.name])) {
            this.formRules[input.name].push(ruleFunc);
          } else {
            this.formRules[input.name] = [ruleFunc];
          }
        }
  
        input.onblur = this.handleValidate.bind(this);
        input.oninput = this.handleClearError.bind(this);
      }
  
      this.formElement.onsubmit = this.handleSubmit.bind(this);
    }
  
    private handleValidate(event: Event): boolean {
      const input = event.target as HTMLInputElement;
      const rules = this.formRules[input.name];
      let errorMessage: string | undefined;
  
      for (const rule of rules) {
        errorMessage = rule(input.value);
        if (errorMessage) break;
      }
  
      if (errorMessage) {
        const formGroup = this.getParent(input, ".form-group");
        if (formGroup) {
          formGroup.classList.add("invalid");
          const formMessage = formGroup.querySelector(".form-message") as HTMLElement;
          if (formMessage) {
            formMessage.innerText = errorMessage;
          }
        }
      }
  
      return !errorMessage;
    }
  
    private handleClearError(event: Event): void {
      const input = event.target as HTMLInputElement;
      const formGroup = this.getParent(input, ".form-group");
      if (formGroup?.classList.contains("invalid")) {
        formGroup.classList.remove("invalid");
        const formMessage = formGroup.querySelector(".form-message") as HTMLElement;
        if (formMessage) {
          formMessage.innerText = "";
        }
      }
    }
  
    private handleSubmit(event: Event): void {
      event.preventDefault();
  
      const inputs = this.formElement.querySelectorAll<HTMLInputElement>("[name][rules]");
      let isValid = true;
  
      for (const input of inputs) {
        if (!this.handleValidate({ target: input })) {
          isValid = false;
        }
      }
  
      if (isValid) {
        const enableInputs = this.formElement.querySelectorAll<HTMLInputElement>("[name]:not([disabled])");
        const formValues = Array.from(enableInputs).reduce<FormValues>((values, input) => {
          switch (input.type) {
            case "radio":
              const checkedRadio = this.formElement.querySelector<HTMLInputElement>(
                `input[name="${input.name}"]:checked`
              );
              values[input.name] = checkedRadio?.value || "";
              break;
            case "checkbox":
              if (!input.matches(":checked")) {
                values[input.name] = "";
                break;
              }
              if (!Array.isArray(values[input.name])) {
                values[input.name] = [];
              }
              (values[input.name] as string[]).push(input.value);
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
        } else {
          this.formElement.submit();
        }
      }
    }
  }
  
  // Khởi tạo Validator
  const form = new Validator("#register-form");
  form.onSubmit = (formData: FormValues) => {
    console.log(formData);
  };