//переписать попапы под новую логинку

const prodRu = Cypress.env("prodRu");
const prodCom = Cypress.env("prodCom");
const password = Cypress.env("password");
const email = "hourly@forautotest.ru"; //email для входа на прод проверять что его не удалили

//главная/оформить подписку в шапке
//account/manage-subscription - на ком должно быть прикрыто. как починят добавить в тесты ино

describe(
  "Проверка доступности страниц на PROD RU и PROD COM",
  {
    retries: 2,
  },
  () => {
    describe("PROD RU", () => {
      function errorLinksRu() {
        var links = [
          `${prodRu}/general/gift-certificates`,
          `${prodRu}/account/purchase`,
        ];
        links.forEach((link) => {
          cy.visit(link, {
            failOnStatusCode: false,
          });
          cy.contains("Ошибка 404");
          cy.contains("Вернуться на главную");
        });
      }

      before(() => {
        cy.visit(`${prodRu}/subscription`);
        cy.contains("Оформить подписку").click()
        cy.clearCookies();
        cy.clearLocalStorage();
      });

      it("Проверка доступности страниц на сайте без авторизации", () => {
        const pagesNonAuthProdRu = {
          "/account/login": "Войти",
          "/account/register": "Зарегистрироваться",
          "/catalog": "Каталог",
          //проверку в каталоге добавить на то что курсы отображаются
          "/subscription": "Оформить подписку",
          "/about/about-project": "О проекте",
          "/info/subscriptions": "Правила использования платной подписки",
          "/account/subscription": "Оформить подписку",
          "/account/manage-subscription": "Войдите или зарегистрируйтесь",
          "/my-courses": "Войдите или зарегистрируйтесь",
          "/account/profile": "Войдите или зарегистрируйтесь",
          //проверка на один курс
          "/course/618be30071b92400278c5be8/0/0/0": "Лекции",
        };

        errorLinksRu();
        for (var url in pagesNonAuthProdRu) {
          var title = pagesNonAuthProdRu[url];
          cy.visit(`${prodRu}${url}`);
          cy.contains(title);
        }
        cy.url().should("include", "/course/618be30071b92400278c5be8/0/0/0");
        cy.get('button[class="afi-button prime"]').click();
        cy.contains("Войдите или зарегистрируйтесь");
      });

      it("Проверка доступности страниц в личном кабинете у пользователя без подписки", () => {
        const pagesPersonalCabinetProdRu = {
          "/catalog": "Каталог",
          //проверку в каталоге добавить на то что курсы отображаются
          "/subscription": "Оформить подписку",
          "/about/about-project": "О проекте",
          "/info/subscriptions": "Правила использования платной подписки",
          "/account/subscription": "Оформить подписку",
          "/account/manage-subscription": "Управление подпиской",
          "/my-courses": "Начатые",
          "/account/profile": "Мой профиль",
          "/account/bonus": "Сертификаты",
          "/thank-you-page": "Ура! Новые лекции уже в личном кабинете!",
          "/subscription/thank-you-page": "Оформить подписку",
          "/onboarding": "Оформить подписку",
          //проверка на один курс
          "/course/618be30071b92400278c5be8/0/0/0": "Лекции",
        };
        cy.visit(prodRu + "/account/login");
        cy.get("form");
        cy.get("input[name='email']").type(email, { force: true });
        cy.get("input[name='password']").type(password, { force: true });
        cy.get("form").submit();
        cy.wait(2000).then(() => {
          cy.url().should("include", "/my-courses");
          errorLinksRu();
          for (var url in pagesPersonalCabinetProdRu) {
            var title = pagesPersonalCabinetProdRu[url];
            cy.visit(`${prodRu}${url}`);
            cy.contains(title);
          }
          cy.url().should("include", "/course/618be30071b92400278c5be8/0/0/0");
          cy.get('button[class="afi-button prime"]').click();
          cy.contains("Выберите тариф");
        });
      });
    });

    describe("PROD COM", () => {
      function errorLinksCom() {
        var links = [
          `${prodCom}/general/gift-certificates`,
          `${prodCom}/account/purchase`,
        ];
        links.forEach((link) => {
          cy.visit(link, {
            failOnStatusCode: false,
          });
          cy.contains("Error 404");
          cy.contains("To main page");
        });
      }

      before(() => {
        cy.visit(`${prodCom}/subscription`);
        cy.clearCookies();
        cy.clearLocalStorage();
      });

      it("Проверка доступности страниц на сайте без авторизации", () => {
        const pagesNonAuthProdCom = {
          "/account/login": "Log in",
          "/account/register": "Sign up",
          "/catalog": "Catalog",
          //проверку в каталоге добавить на то что курсы отображаются
          "/subscription": "Self-improvement platform",
          "/account/subscription": "Get unlimited access",
          "/info/subscriptions": "Paid Subscription usage rules",
          "/about/about-project": "About the project",
          "/my-courses": "Log in or sign up",
          "/account/profile": "Log in or sign up",
          //проверка на один курс
          "/course/63edce1cbcb16f0028018798/0/0/0": "Progress",
        };
        errorLinksCom();
        for (var url in pagesNonAuthProdCom) {
          var title = pagesNonAuthProdCom[url];
          cy.visit(`${prodCom}${url}`);
          cy.contains(title);
        }
        cy.url().should("include", "/course/63edce1cbcb16f0028018798/0/0/0");
        cy.get('button[class="afi-button prime"]').click();
        cy.contains("Log in or sign up");
      });

      it("Проверка доступности страниц в личном кабинете у пользователя", () => {
        const pagesPersonalCabinetProdCom = {
          "/catalog": "Catalog",
          //проверку в каталоге добавить на то что курсы отображаются
          "/subscription": "Self-improvement platform",
          "/account/subscription": "Get unlimited access",
          "/info/subscriptions": "Paid Subscription usage rules",
          "/about/about-project": "About the project",
          "/account/profile": "Profile",
          "/my-courses": "Started",
          "/account/bonus": "Gift certificate",
          "/thank-you-page": "New lectures are already in your account!",
          "/subscription/thank-you-page": "Hey! Happy to have you here!",
          //проверка на один курс
          "/course/63edce1cbcb16f0028018798/0/0/0": "Progress",
        };

        cy.visit(prodCom + "/account/login");
        cy.get("form");
        cy.get("input[name='email']").type(email, { force: true });
        cy.get("input[name='password']").type(password, { force: true });
        cy.get("form").submit();
        cy.wait(2000).then(() => {
          cy.url().should("include", "/my-courses");
          errorLinksCom();
          for (var url in pagesPersonalCabinetProdCom) {
            var title = pagesPersonalCabinetProdCom[url];
            cy.visit(`${prodCom}${url}`);
            cy.contains(title);
          }
          cy.url().should("include", "/course/63edce1cbcb16f0028018798/0/0/0");
          cy.get('button[class="afi-button prime"]').click();
          cy.contains("Choose a plan");
        });
      });
    });
  }
);

/*describe(
  "Проверка открытия попапа оплаты подписки на /subscription PROD RU и PROD COM",
  {
    retries: 2,
  },
  () => {
    describe("PROD RU", () => {
      const ru1Email =
        "test" + Math.floor(Math.random() * 10000001) + "@forautotest.ru";
      const ru2Email =
        "test" + Math.floor(Math.random() * 10000001) + "@forautotest.ru";
      beforeEach(() => {
        cy.visit(prodRu);
        cy.clearCookies();
        cy.clearLocalStorage();
      });

      it("Регистрация пользователя после выбора тарифа", () => {
        cy.visit(prodRu + "/subscription");
        //выбор тарифа на месяц и регистрация пользователя
        cy.get("div[class='tariff-block ng-star-inserted']:first").within(
          () => {
            cy.get(
              'button[class="afi-btn afi-primary afi-btn-sm tariff-block-button"]'
            ).click({ force: true });
          }
        );
        cy.contains("Войдите или зарегистрируйтесь");
        cy.contains("Зарегистрироваться");
        cy.get("form");
        cy.get("input[name='email']").type(ru1Email, {
          force: true,
        });
        cy.get("input[name='password']").type(password, { force: true });
        cy.get("form").submit();
        cy.wait(5000).then(() => {
          cy.log("Открытие попапа для оплаты");
          cy.contains("Подписка на 1 месяц");
        });
      });

      it("Авторизация пользователя после выбора тарифа", () => {
        cy.visit(prodRu + "/subscription");
        //выбор тарифа и авторизация пользователя
        cy.get("div[class='tariff-block ng-star-inserted']:first").within(
          () => {
            cy.get(
              'button[class="afi-btn afi-primary afi-btn-sm tariff-block-button"]'
            ).click({ force: true });
          }
        );
        cy.contains("Войдите или зарегистрируйтесь");
        cy.contains("Вход").click();
        cy.contains("Войти");
        cy.get("form");
        cy.get("input[name='email']").type(ru1Email, {
          force: true,
        });
        cy.get("input[name='password']").type(password, { force: true });
        cy.get("form").submit();
        cy.wait(5000).then(() => {
          cy.log("Открытие попапа для оплаты");
          cy.contains("Подписка на 1 месяц");
        });
      });

      it("Свежезареганный пользователь", () => {
        cy.visit(prodRu + "/account/register");
        cy.contains("Войдите или зарегистрируйтесь");
        cy.get("form");
        cy.get("input[name='email']").type(ru2Email, {
          force: true,
        });
        cy.get("input[name='password']").type(password, { force: true });
        cy.get("form").submit();
        cy.wait(3000).then(() => {
          cy.contains("Ура! Вы успешно зарегистрировались");
          cy.visit(prodRu + "/subscription");
          //выбор тарифа
          cy.get("div[class='tariff-block ng-star-inserted']:first").within(
            () => {
              cy.get(
                'button[class="afi-btn afi-primary afi-btn-sm tariff-block-button"]'
              ).click({ force: true });
            }
          );
          cy.log("Открытие попапа для оплаты");
          cy.contains("Подписка на 1 месяц");
        });
      });

      it("Открытие тарифов уже авторизованным пользователем", () => {
        cy.visit(prodRu + "/account/login");
        cy.contains("Войдите или зарегистрируйтесь");
        cy.get("form");
        cy.get("input[name='email']").type(ru2Email, {
          force: true,
        });
        cy.get("input[name='password']").type(password, { force: true });
        cy.get("form").submit();
        cy.wait(3000).then(() => {
          cy.url().should("include", "/my-courses");
          cy.visit(prodRu + "/subscription");
          //выбор тарифа
          cy.get("div[class='tariff-block ng-star-inserted']:first").within(
            () => {
              cy.get(
                'button[class="afi-btn afi-primary afi-btn-sm tariff-block-button"]'
              ).click({ force: true });
            }
          );
          cy.log("Открытие попапа для оплаты");
          cy.contains("Подписка на 1 месяц");
        });
      });
    });

    describe("PROD COM", () => {
      const com1Email =
        "test" + Math.floor(Math.random() * 10000001) + "@forautotest.ru";
      const com2Email =
        "test" + Math.floor(Math.random() * 10000001) + "@forautotest.ru";
      beforeEach(() => {
        cy.visit(prodCom);
        cy.clearCookies();
        cy.clearLocalStorage();
      });

      it("Регистрация пользователя после выбора тарифа", () => {
        cy.visit(prodCom + "/subscription");
        //выбор тарифа и регистрация пользователя
        cy.get("div[class='tariff-block ng-star-inserted']:first").within(
          () => {
            cy.get(
              'button[class="afi-btn afi-primary afi-btn-sm tariff-block-button"]'
            ).click({ force: true });
          }
        );
        cy.contains("Log in or sign up");
        cy.contains("Log in").click();
        cy.contains("Forgot your password?");
        cy.get("form");
        cy.get("input[name='email']").type(com1Email, {
          force: true,
        });
        cy.get("input[name='password']").type(password, { force: true });
        cy.get("form").submit();
        cy.wait(5000).then(() => {
          cy.log("Открытие попапа для оплаты");
          cy.contains("Subscription to 3 month");
        });
      });

      it("Авторизация пользователя после выбора тарифа", () => {
        cy.visit(prodCom + "/subscription");
        //выбор тарифа и авторизация пользователя
        cy.get("div[class='tariff-block ng-star-inserted']:first").within(
          () => {
            cy.get(
              'button[class="afi-btn afi-primary afi-btn-sm tariff-block-button"]'
            ).click({ force: true });
          }
        );
        cy.contains("Log in or sign up");
        cy.get("form");
        cy.get("input[name='email']").type(com1Email, {
          force: true,
        });
        cy.get("input[name='password']").type(password, { force: true });
        cy.get("form").submit();
        cy.wait(5000).then(() => {
          cy.log("Открытие попапа для оплаты");
          cy.contains("Subscription to 3 month");
        });
      });

      it("Свежезареганный пользователь", () => {
        cy.visit(prodCom + "/account/register");
        cy.contains("Log in or sign up");
        cy.get("form");
        cy.get("input[name='email']").type(com2Email, {
          force: true,
        });
        cy.get("input[name='password']").type(password, { force: true });
        cy.get("form").submit();
        cy.wait(3000).then(() => {
          cy.contains("Hurrah! You have successfully registered");
          cy.visit(prodCom + "/subscription");
          //выбор тарифа
          cy.get("div[class='tariff-block ng-star-inserted']:first").within(
            () => {
              cy.get(
                'button[class="afi-btn afi-primary afi-btn-sm tariff-block-button"]'
              ).click({ force: true });
            }
          );
          cy.log("Открытие попапа для оплаты");
          cy.contains("Subscription to 3 month");
        });
      });

      it("Открытие тарифов уже авторизованным пользователем", () => {
        cy.visit(prodCom + "/account/login");
        cy.contains("Log in or sign up");
        cy.get("form");
        cy.get("input[name='email']").type(com2Email, {
          force: true,
        });
        cy.get("input[name='password']").type(password, { force: true });
        cy.get("form").submit();
        cy.wait(3000).then(() => {
          cy.url().should("include", "/my-courses");
          cy.visit(prodCom + "/subscription");
          //выбор тарифа
          cy.get("div[class='tariff-block ng-star-inserted']:first").within(
            () => {
              cy.get(
                'button[class="afi-btn afi-primary afi-btn-sm tariff-block-button"]'
              ).click({ force: true });
            }
          );
          cy.log("Открытие попапа для оплаты");
          cy.contains("Subscription to 3 month");
        });
      });
    });
  }
);*/
