// import * as validators from "@vuelidate/validators";
// import { createI18n } from "vue-i18n";

// const minLength = validators.minLength(1);
// const $message = (_ref) => {
//   let { $params } = _ref;
//   return `This field should be at least ${$params.min} characters`;
// };

// const { createI18nMessage } = validators;

// console.log(validators.email);

export const messages = {
  en: {
    validations: {
      required: "This value is required.",
      minLength: ($params) =>
        `This field has a minimum length of ${$params.min}.`,
      email: "This field must be a valid email address",
      maxLength: ($params) =>
        `This field has a maximum length of ${$params.max}.`,
    },
  },
  es: {
    validations: {
      required: "Este campo es requerido.",
      minLength: ($params) =>
        `Este campo posee una longitud mínima de ${$params.min}.`,
      email: "Este campo debe ser un correo electrónico válido.",
      maxLength: ($params) =>
        `Este campo posee una longitud máxima de ${$params.max}.`,
    },
  },
};

// const i18n = createI18n({
//   locale: "es",
//   fallbackLocale: "en",
//   messages,
// });

// const withI18nMessage = createI18nMessage({ t: i18n.global.t.bind(i18n) });
// export const required = withI18nMessage(validators.required);
// export const email = withI18nMessage(validators.email);
