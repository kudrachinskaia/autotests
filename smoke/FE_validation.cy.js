//пароль будет по другому валидироваться

const ruUrl = Cypress.env("devRu");
const comUrl = Cypress.env("devCom");

/*
валидация на то, что ты ВХОДИШЬ
типа если ты регалась с Natali@gmail.com
а входишь как natali@gmail.com
то не войдешь теперь
*/
function validateEmail(email, color) {
  cy.get("input[name='email']")
    .type(email)
    .should("have.css", "border-color", color);
}

function validatePassword(password, color) {
  cy.get("input[name='password']")
    .type(password)
    .should("have.css", "border-color", color);
}

function submitForm(email, password) {
  cy.get("input[name='email']").type(email);
  cy.get("input[name='password']").type(password);
  cy.get("form").submit();
}

describe("RU", () => {
  beforeEach(() => {
    cy.visit(ruUrl);
    cy.clearCookies();
    cy.clearLocalStorage();
  });

  it("Регистрация", () => {
    cy.visit(ruUrl + "/account/register");
    cy.get("form");

    cy.log("Валидация поля email");

    const emails = [
      "я",
      "shfiorhfoiewfi",
      "яруский@gmail.com",
      " ",
      "q",
      "$",
      "1",
      "steam@mail",
      "steam@mail.",
      "steam@mail.r",
      "steammail.ru",
      "steam @mail.ru",
    ];
    for (const email of emails) {
      validateEmail(email, "rgb(255, 59, 48)");
      cy.contains("Неправильный формат email");
      cy.get("input[name='email']").clear();
      cy.contains("Это поле обязательное");
    }
    validateEmail("steam@mail.ru", "rgb(52, 199, 89)");
    cy.get("input[name='email']").clear();
    cy.contains("Это поле обязательное");

    validateEmail("STEAM@MAIL.RU", "rgb(52, 199, 89)");
    cy.get("input[name='email']").clear();

    cy.log("Валидация поля password");

    validatePassword(
      "Dlanbf2r9mhn6ku79okqgflknp62hmur9ombb5pyboy5d6mzf2g1vwj4mtirzdpvxi4cf4mxn70516v8gd6vc3ysyvhe4cd332oedpqskfqckuqzwyb5p0y9zojb4ok789zyfccyl24dlkogzwlpl05djmd5csf9isn2m5t0xzsmd6nqy2w4r5qvgtom0vbxb5mvssnodalt00bmtei",
      "rgb(52, 199, 89)"
    );
    cy.get("input[name='password']").clear();

    validatePassword("Abcdef1", "rgb(52, 199, 89)");
    cy.get("input[name='password']").clear();
    cy.contains("Это поле обязательное");

    validatePassword("111", "rgb(255, 59, 48)");
    cy.contains("Минимум 5 знаков");
    cy.get("input[name='password']").clear();

    validatePassword("abcdef1", "rgb(255, 59, 48)");
    cy.contains("Минимум 1 заглавная буква");
    cy.get("input[name='password']").clear();

    validatePassword("Abcdefg", "rgb(255, 59, 48)");
    cy.contains("Минимум 1 цифра");
    cy.get("input[name='password']").clear();

    cy.log("Валидация полей email and password вместе");

    cy.get("input[name='email']").clear();
    cy.get("input[name='password']").clear();

    var newEmail =
      "testart+" + Math.floor(Math.random() * 10000001) + "@getnada.com";
    var correctPassword = "Abcdef1";

    submitForm(newEmail, correctPassword);
    cy.wait(5000).then(() => {
      cy.contains("Ура! Вы успешно зарегистрировались");
      cy.clearCookies();
      cy.clearLocalStorage().then(() => {
        cy.visit(ruUrl + "/account/register");

        submitForm(newEmail, correctPassword);
        cy.contains("Введенный email уже используется");
        cy.get("input[name='email']").should(
          "have.css",
          "border-color",
          "rgb(255, 59, 48)"
        );
      });
    });
  });
/*
  it("Авторизация", () => {
    cy.visit(ruUrl + "/account/login");
    cy.get("form");

    cy.log("Валидация поля email");

    const emails = [
      "я",
      "shfiorhfoiewfi",
      "яруский@gmail.com",
      " ",
      "q",
      "$",
      "1",
      "steam@mail",
      "steam@mail.",
      "steam@mail.r",
      "steammail.ru",
      "steam @mail.ru",
    ];
    for (const email of emails) {
      validateEmail(email, "rgb(255, 59, 48)");
      cy.contains("Неправильный формат email");
      cy.get("input[name='email']").clear();
      cy.contains("Это поле обязательное");
    }
    validateEmail("steam@mail.ru", "rgb(52, 199, 89)");
    cy.get("input[name='email']").clear();
    cy.contains("Это поле обязательное");

    validateEmail("STEAM@MAIL.RU", "rgb(52, 199, 89)");
    cy.get("input[name='email']").clear();
    cy.contains("Это поле обязательное");

    cy.log("Валидация поля password");

    validatePassword("111", "rgb(255, 59, 48)");
    cy.contains("Минимум 5 знаков");
    cy.get("input[name='password']").clear();
    cy.contains("Это поле обязательное");

    validatePassword(
      "lanbf2r9mhn6ku79okqgflknp62hmur9ombb5pyboy5d6mzf2g1vwj4mtirzdpvxi4cf4mxn70516v8gd6vc3ysyvhe4cd332oedpqskfqckuqzwyb5p0y9zojb4ok789zyfccyl24dlkogzwlpl05djmd5csf9isn2m5t0xzsmd6nqy2w4r5qvgtom0vbxb5mvssnodalt00bmtei",
      "rgb(52, 199, 89)"
    );
    cy.get("input[name='password']").clear();
    cy.contains("Это поле обязательное");

    validatePassword("11111", "rgb(52, 199, 89)");

    cy.log("Валидация полей email and password вместе");

    cy.get("input[name='email']").clear();
    cy.get("input[name='password']").clear();

    submitForm("valid@test.ru", "55555"); //сделать данные динамичными
    cy.contains("Проверьте правильность введенных данных");
    cy.get("input[name='email']").clear();
    cy.get("input[name='password']").clear();

    submitForm(
      "testart+" + Math.floor(Math.random() * 10000001) + "@getnada.com",
      "11111"
    );
    cy.get("input[name='email']").should(
      "have.css",
      "border-color",
      "rgb(255, 59, 48)"
    );
    cy.contains("Проверьте правильность введенных данных");

    cy.get("input[name='email']").clear();
    cy.get("input[name='password']").clear();
    submitForm("valid@test.ru", "Abcdef1"); //сделать данные динамичными
    cy.wait(7000).then(() => {
      cy.url().should("include", "/my-courses");
    });
  });

  it("Сброс пароля - поле email", () => {
    cy.visit(ruUrl + "/account/login");
    cy.contains("Забыли пароль?").click();
    cy.get("form");

    cy.log("Валидация поля email");

    const emails = [
      "я",
      "shfiorhfoiewfi",
      "яруский@gmail.com",
      " ",
      "q",
      "$",
      "1",
      "steam@mail",
      "steam@mail.",
      "steam@mail.r",
      "steammail.ru",
      "steam @mail.ru",
    ];
    for (const email of emails) {
      validateEmail(email, "rgb(255, 59, 48)");
      cy.contains("Неправильный формат email");
      cy.get("input[name='email']").clear();
      cy.contains("Это поле обязательное");
    }
    validateEmail("steam@mail.ru", "rgb(52, 199, 89)");
    cy.get("input[name='email']").clear();
    cy.contains("Это поле обязательное");

    cy.get("input[name='email']").type("STEAjyjvM@MAIL.RU");
    cy.get("form").submit();
    cy.get("input[name='email']").should(
      "have.css",
      "border-color",
      "rgb(255, 59, 48)"
    );
    cy.contains("Проверьте правильность введенных данных");
    cy.get("input[name='email']").clear();

    cy.get("input[name='email']").type("sbros@test.ru");
    cy.get("form").submit();
    cy.contains("Успешно!");
    cy.contains(
      "Ссылку на восстановление пароля мы отправили на указанную вами почту"
    );
  });*/
});

describe("COM", () => {
  beforeEach(() => {
    cy.visit(comUrl);
    cy.clearCookies();
    cy.clearLocalStorage();
  });

  it("Регистрация", () => {
    cy.visit(comUrl + "/account/register");
    cy.get("form");

    cy.log("Валидация поля email");

    const emails = [
      "я",
      "shfiorhfoiewfi",
      "яруский@gmail.com",
      " ",
      "q",
      "$",
      "1",
      "steam@mail",
      "steam@mail.",
      "steam@mail.r",
      "steammail.ru",
      "steam @mail.ru",
    ];
    for (const email of emails) {
      validateEmail(email, "rgb(255, 59, 48)");
      cy.contains("Wrong email format");
      cy.get("input[name='email']").clear();
      cy.contains("Required field");
    }
    validateEmail("steam@mail.ru", "rgb(52, 199, 89)");
    cy.get("input[name='email']").clear();
    cy.contains("Required field");

    validateEmail("STEAM@MAIL.RU", "rgb(52, 199, 89)");
    cy.get("input[name='email']").clear();
    cy.contains("Required field");

    cy.log("Валидация поля password");

    validatePassword(
      "Dlanbf2r9mhn6ku79okqgflknp62hmur9ombb5pyboy5d6mzf2g1vwj4mtirzdpvxi4cf4mxn70516v8gd6vc3ysyvhe4cd332oedpqskfqckuqzwyb5p0y9zojb4ok789zyfccyl24dlkogzwlpl05djmd5csf9isn2m5t0xzsmd6nqy2w4r5qvgtom0vbxb5mvssnodalt00bmtei",
      "rgb(52, 199, 89)"
    );
    cy.get("input[name='password']").clear();

    validatePassword("Abcdef1", "rgb(52, 199, 89)");
    cy.get("input[name='password']").clear();

    validatePassword("111", "rgb(255, 59, 48)");
    cy.contains("Minimum 5 characters");

    validatePassword("abcdef1", "rgb(255, 59, 48)");
    cy.contains("Minimum 1 capital letter");
    cy.get("input[name='password']").clear();

    validatePassword("Abcdefg", "rgb(255, 59, 48)");
    cy.contains("Minimum 1 digit");
    cy.get("input[name='password']");

    cy.get("input[name='password']").clear();
    cy.contains("Required field");

    cy.log("Валидация полей email and password вместе");

    cy.get("input[name='email']").clear();

    var newEmail =
      "testart+" + Math.floor(Math.random() * 10000001) + "@getnada.com";
    var correctPassword = "Abcdef1";
    submitForm(newEmail, correctPassword);
    cy.wait(5000).then(() => {
      cy.contains("Hurrah! You have successfully registered");

      cy.clearCookies();
      cy.clearLocalStorage().then(() => {
        cy.visit(comUrl + "/account/register");
        submitForm(newEmail, correctPassword);
        cy.contains("The entered email is already in use");
        cy.get("input[name='email']").should(
          "have.css",
          "border-color",
          "rgb(255, 59, 48)"
        );
      });
    });
  });
/*
  it("Авторизация", () => {
    cy.visit(comUrl + "/account/login");
    cy.get("form");

    cy.log("Валидация поля email");

    const emails = [
      "я",
      "shfiorhfoiewfi",
      "яруский@gmail.com",
      " ",
      "q",
      "$",
      "1",
      "steam@mail",
      "steam@mail.",
      "steam@mail.r",
      "steammail.ru",
      "steam @mail.ru",
    ];
    for (const email of emails) {
      validateEmail(email, "rgb(255, 59, 48)");
      cy.contains("Wrong email format");
      cy.get("input[name='email']").clear();
      cy.contains("Required field");
    }
    validateEmail("steam@mail.ru", "rgb(52, 199, 89)");
    cy.get("input[name='email']").clear();
    cy.contains("Required field");

    validateEmail("STEAM@MAIL.RU", "rgb(52, 199, 89)");
    cy.get("input[name='email']").clear();

    cy.log("Валидация поля password");

    validatePassword("111", "rgb(255, 59, 48)");
    cy.contains("Minimum 5 characters");
    cy.get("input[name='password']").clear();
    cy.contains("Required field");

    validatePassword(
      "lanbf2r9mhn6ku79okqgflknp62hmur9ombb5pyboy5d6mzf2g1vwj4mtirzdpvxi4cf4mxn70516v8gd6vc3ysyvhe4cd332oedpqskfqckuqzwyb5p0y9zojb4ok789zyfccyl24dlkogzwlpl05djmd5csf9isn2m5t0xzsmd6nqy2w4r5qvgtom0vbxb5mvssnodalt00bmtei",
      "rgb(52, 199, 89)"
    );
    cy.get("input[name='password']").clear();
    cy.contains("Required field");

    validatePassword("11111", "rgb(52, 199, 89)");

    cy.log("Валидация полей email and password вместе");

    cy.get("input[name='email']").clear();
    cy.get("input[name='password']").clear();

    submitForm("valid@test.ru", "55555");
    cy.contains("Check the entered data");
    cy.get("input[name='password']")
      .should("have.css", "border-color", "rgb(255, 59, 48)")
      .clear();
    cy.get("input[name='password']").clear();

    submitForm(
      "testart+" + Math.floor(Math.random() * 10000001) + "@getnada.com",
      "11111"
    );
    cy.get("input[name='email']").should(
      "have.css",
      "border-color",
      "rgb(255, 59, 48)"
    );
    cy.contains("Check the entered data");

    cy.get("input[name='email']").clear();
    cy.get("input[name='password']").clear();
    submitForm("valid@test.ru", "11111");
    //  cy.url().should("include", "/my-courses");
  });

  it("Сброс пароля - поле email", () => {
    cy.visit(comUrl + "/account/login");
    cy.contains("Forgot your password?").click();
    cy.get("form");

    cy.log("Валидация поля email");

    const emails = [
      "я",
      "shfiorhfoiewfi",
      "яруский@gmail.com",
      " ",
      "q",
      "$",
      "1",
      "steam@mail",
      "steam@mail.",
      "steam@mail.r",
      "steammail.ru",
      "steam @mail.ru",
    ];
    for (const email of emails) {
      validateEmail(email, "rgb(255, 59, 48)");
      cy.contains("Wrong email format");
      cy.get("input[name='email']").clear();
      cy.contains("Required field");
    }
    validateEmail("steam@mail.ru", "rgb(52, 199, 89)");
    cy.get("input[name='email']").clear();
    cy.contains("Required field");

    cy.get("input[name='email']").type("STEAjyjvM@MAIL.RU");
    cy.get("form").submit();
    cy.get("input[name='email']").should(
      "have.css",
      "border-color",
      "rgb(255, 59, 48)"
    );
    cy.contains("Check the entered data");
    cy.get("input[name='email']").clear();

    cy.get("input[name='email']").type("sbros@test.ru");
    cy.get("form").submit();
    cy.contains("Successful!");
    cy.contains(
      "We have sent a link to password recovery to the email address you specified"
    );
  });*/
});
