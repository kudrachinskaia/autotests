//не актуально

const ruUrl = Cypress.env("devRu");
const comUrl = Cypress.env("devCom");

const correctPassword = "password";
//не работает кнопка отправки сброса пароля
function typeEmail(email, color) {
  cy.get("input[type='text']")
    .type(email)
    .should("have.css", "border-color", color);
}

function typePassword(password, color) {
  cy.get("input[type='password']")
    .type(password)
    .should("have.css", "border-color", color);
}

function submitForm(email, password) {
  cy.get("input[name='email']").type(email);
  cy.get("input[name='password']").type(password);
  cy.get("form").submit();
}

const wrongEmails = [
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

describe("RU", () => {
  function ruEmailValidation(page, path) {
    cy.log(`Валидация поля email на странице ${page}`);

    cy.visit(ruUrl + path);
    cy.get("form");

    for (const email of wrongEmails) {
      typeEmail(email, "rgb(32, 32, 32)");
      cy.contains("Неправильный формат email");
      cy.get("input[name='email']").clear();
      cy.contains("Это поле обязательное");
    }
    //нормальная почта
    typeEmail("steam@mail.ru", "rgb(52, 199, 89)");
    cy.get("input[name='email']").clear();
    cy.contains("Это поле обязательное");
    //почта капсом
    typeEmail("QWERTY@ZXC.RU", "rgb(52, 199, 89)");
    cy.get("input[name='email']").clear();
  }

  /*beforeEach(() => {
    cy.visit(ruUrl);
    cy.clearCookies();
    cy.clearLocalStorage();
  });*/

  it.only("Регистрация", () => {
    ruEmailValidation("регистрации", "/account/register");

    cy.log("Валидация поля password");

    typePassword(
      "Dlanbf2r9mhn6ku79okqgflknp62hmur9ombb5pyboy5d6mzf2g1vwj4mtirzdpvxi4cf4mxn70516v8gd6vc3ysyvhe4cd332oedpqskfqckuqzwyb5p0y9zojb4ok789zyfccyl24dlkogzwlpl05djmd5csf9isn2m5t0xzsmd6nqy2w4r5qvgtom0vbxb5mvssnodalt00bmtei",
      "rgb(52, 199, 89)"
    );
    cy.get("input[name='password']").clear();

    typePassword("11111", "rgb(52, 199, 89)");
    cy.get("input[name='password']").clear();
    cy.contains("Это поле обязательное");

    typePassword("111", "rgb(255, 59, 48)");
    cy.contains("Минимум 5 знаков");
    cy.get("input[name='password']").clear();

    cy.log("Валидация полей email and password вместе");

    cy.get("input[name='email']").clear();
    cy.get("input[name='password']").clear();

    var newEmail =
      "testart" + Math.floor(Math.random() * 1000000001) + "@forautotest.ru";

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

  it("Авторизация", () => {
    ruEmailValidation("авторизации", "/account/login");

    cy.log("Валидация поля password");

    typePassword("111", "rgb(255, 59, 48)");
    cy.contains("Минимум 5 знаков");
    cy.get("input[name='password']").clear();
    cy.contains("Это поле обязательное");

    typePassword(
      "lanbf2r9mhn6ku79okqgflknp62hmur9ombb5pyboy5d6mzf2g1vwj4mtirzdpvxi4cf4mxn70516v8gd6vc3ysyvhe4cd332oedpqskfqckuqzwyb5p0y9zojb4ok789zyfccyl24dlkogzwlpl05djmd5csf9isn2m5t0xzsmd6nqy2w4r5qvgtom0vbxb5mvssnodalt00bmtei",
      "rgb(52, 199, 89)"
    );
    cy.get("input[name='password']").clear();
    cy.contains("Это поле обязательное");

    typePassword("11111", "rgb(52, 199, 89)");

    cy.log("Валидация полей email and password вместе");

    cy.get("input[name='email']").clear();
    cy.get("input[name='password']").clear();

    //существующий пользователь с неправильным паролем
    cy.registerAndGetUserData().then((newUser) => {
      submitForm(newUser.email, "657646747");
      cy.contains("Проверьте правильность введенных данных");

      cy.get("input[name='email']").clear();
      cy.get("input[name='password']").clear();

      //не зарегистрированная почта
      submitForm(
        "testart" + Math.floor(Math.random() * 1000000001) + "@forautotest.ru",
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

      //правильные данные
      submitForm(newUser.email, correctPassword);
      cy.wait(7000).then(() => {
        cy.url().should("include", "/my-courses");
      });
    });
  });

  /*it("Сброс пароля - поле email", () => {
    cy.visit(ruUrl + "/account/login");
    cy.contains("Забыли пароль?").click();
    cy.get("form");

    cy.log("Валидация поля email");

    for (const email of wrongEmails) {
      typeEmail(email, "rgb(255, 59, 48)");
      cy.contains("Неправильный формат email");
      cy.get("input[name='email']").clear();
      cy.contains("Это поле обязательное");
    }
    //нормальная почта
    typeEmail("steam@mail.ru", "rgb(52, 199, 89)");
    cy.get("input[name='email']").clear();
    cy.contains("Это поле обязательное");

    //почта капсом
    typeEmail("QWERTY@ZXC.RU", "rgb(52, 199, 89)");
    cy.get("input[name='email']").clear();

    //НЕсуществующий пользователь сбрасывает пароль
    const noExistUser =
      "test" + Math.floor(Math.random() * 1000000001) + "@forautotest.ru";
    cy.get("input[name='email']").type(noExistUser);
    cy.get("form").submit();
    cy.get("input[name='email']").should(
      "have.css",
      "border-color",
      "rgb(255, 59, 48)"
    );
    cy.contains("Проверьте правильность введенных данных");
    cy.get("input[name='email']").clear();

    //существующий пользователь сбрасывает пароль
    cy.registerAndGetUserData().then((newUser) => {
      cy.get("input[name='email']").type(newUser.email);
      cy.get("form").submit(); //не работает
      cy.contains("Успешно!");
      cy.contains(
        "Ссылку на восстановление пароля мы отправили на указанную вами почту"
      );
    });
  });*/
});

describe("COM", () => {
  function ComEmailValidation(page, path) {
    cy.log(`Валидация поля email на странице ${page}`);

    cy.visit(comUrl + path);
    cy.get("form");

    for (const email of wrongEmails) {
      typeEmail(email, "rgb(255, 59, 48)");
      cy.contains("Wrong email format");
      cy.get("input[name='email']").clear();
      cy.contains("Required field");
    }
    typeEmail("steam@mail.ru", "rgb(52, 199, 89)");
    cy.get("input[name='email']").clear();
    cy.contains("Required field");

    typeEmail("STEAM@MAIL.RU", "rgb(52, 199, 89)");
    cy.get("input[name='email']").clear();
    cy.contains("Required field");
  }

  beforeEach(() => {
    cy.visit(comUrl);
    cy.clearCookies();
    cy.clearLocalStorage();
  });

  it("Регистрация", () => {
    ComEmailValidation("register", "/account/register");

    cy.log("Валидация поля password");

    typePassword(
      "Dlanbf2r9mhn6ku79okqgflknp62hmur9ombb5pyboy5d6mzf2g1vwj4mtirzdpvxi4cf4mxn70516v8gd6vc3ysyvhe4cd332oedpqskfqckuqzwyb5p0y9zojb4ok789zyfccyl24dlkogzwlpl05djmd5csf9isn2m5t0xzsmd6nqy2w4r5qvgtom0vbxb5mvssnodalt00bmtei",
      "rgb(52, 199, 89)"
    );
    cy.get("input[name='password']").clear();

    typePassword("11111", "rgb(52, 199, 89)");
    cy.get("input[name='password']").clear();

    typePassword("111", "rgb(255, 59, 48)");
    cy.contains("Minimum 5 characters");

    cy.get("input[name='password']").clear();
    cy.contains("Required field");

    cy.log("Валидация полей email and password вместе");

    cy.get("input[name='email']").clear();

    var newEmail =
      "testart" + Math.floor(Math.random() * 1000000001) + "@forautotest.ru";
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

  it("Авторизация", () => {
    ComEmailValidation("login", "/account/login");

    cy.log("Валидация поля password");

    typePassword("111", "rgb(255, 59, 48)");
    cy.contains("Minimum 5 characters");
    cy.get("input[name='password']").clear();
    cy.contains("Required field");

    typePassword(
      "lanbf2r9mhn6ku79okqgflknp62hmur9ombb5pyboy5d6mzf2g1vwj4mtirzdpvxi4cf4mxn70516v8gd6vc3ysyvhe4cd332oedpqskfqckuqzwyb5p0y9zojb4ok789zyfccyl24dlkogzwlpl05djmd5csf9isn2m5t0xzsmd6nqy2w4r5qvgtom0vbxb5mvssnodalt00bmtei",
      "rgb(52, 199, 89)"
    );
    cy.get("input[name='password']").clear();
    cy.contains("Required field");

    typePassword("11111", "rgb(52, 199, 89)");

    cy.log("Валидация полей email and password вместе");

    cy.get("input[name='email']").clear();
    cy.get("input[name='password']").clear();

    //существующий пользователь с неправильным паролем
    cy.registerAndGetUserData().then((newUser) => {
      submitForm(newUser.email, "hgchgcjkh");
      cy.contains("Check the entered data");

      cy.get("input[name='email']").clear();
      cy.get("input[name='password']").clear();

      //не зарегистрированная почта
      submitForm(
        "testart" + Math.floor(Math.random() * 10000001) + "@forautotest.ru",
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

      //правильные данные
      submitForm(newUser.email, correctPassword);
      cy.wait(7000).then(() => {
        cy.url().should("include", "/my-courses");
      });
    });
  });

  /*it("Сброс пароля - поле email", () => {
    cy.visit(comUrl + "/account/login");
    cy.contains("Forgot your password?").click();
    cy.get("form");

    cy.log("Валидация поля email");

    for (const email of wrongEmails) {
      typeEmail(email, "rgb(255, 59, 48)");
      cy.contains("Wrong email format");
      cy.get("input[name='email']").clear();
      cy.contains("Required field");
    }
    //нормальная почта
    typeEmail("steam@mail.ru", "rgb(52, 199, 89)");
    cy.get("input[name='email']").clear();
    cy.contains("Required field");

    //почта капсом
    typeEmail("QWERTY@ZXC.RU", "rgb(52, 199, 89)");
    cy.get("input[name='email']").clear();

    //НЕсуществующий пользователь сбрасывает пароль
    const noExistUser =
      "test" + Math.floor(Math.random() * 1000000001) + "@forautotest.ru";
    cy.get("input[name='email']").type(noExistUser);
    cy.get(
      'button[class="afi-btn afi-primary afi-btn-sm auth-submit-button ng-star-inserted"]'
    ).click();
    //cy.wait(2000).then(() => {
    cy.get("input[name='email']").should(
      "have.css",
      "border-color",
      "rgb(255, 59, 48)"
    );
    cy.contains("Check the entered data");
    cy.get("input[name='email']").clear();

    //существующий пользователь сбрасывает пароль
    cy.registerAndGetUserData().then((newUser) => {
      cy.get("input[name='email']").type(newUser.email);
      cy.get("form").submit(); //не работает
      cy.contains("Successful!");
      cy.contains(
        "We have sent a link to password recovery to the email address you specified"
      );
      // });
    });
  });*/
});
