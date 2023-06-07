//надо переписывать

const devRu = Cypress.env("devRu");
const devCom = Cypress.env("devCom");
const password = Cypress.env("password");

describe(
  "Проверка доступности страниц на DEV RU и DEV COM",
  {
    retries: 1,
  },
  () => {
    describe("DEV RU", () => {
      function errorLinksRu() {
        var links = [
          `${devRu}/general/gift-certificates`,
          `${devRu}/account/purchase`,
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
        cy.visit(devRu);
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
          cy.visit(`${devRu}${url}`);
          cy.contains(title);
        }
        cy.url().should("include", "/course/618be30071b92400278c5be8/0/0/0");
        cy.get('button[class="afi-button prime"]').click();
        cy.url().should("include", "/subscription");
      });

      it("Проверка доступности страниц в личном кабинете у пользователя с подпиской", () => {
        const pagesPersonalCabinetDevRu = {
          "/catalog": "Каталог",
          "/subscription": "Оформить подписку",
          "/about/about-project": "О проекте",
          "/info/subscriptions": "Правила использования платной подписки",
          "/account/subscription": "Оформить подписку",
          "/account/manage-subscription": "Управление подпиской",
          "/my-courses": "Начатые",
          "/account/profile": "Мой профиль",
          "/account/bonus": "Сертификаты",
          "/thank-you-page": "Ура! Новые лекции уже в личном кабинете!",
          "/onboarding": "Станьте лучше",
          //проверка на один курс
          "/course/618be30071b92400278c5be8/0/0/0": "Лекции",
        };

        cy.getTariff("month").then((monthTariff) => {
          cy.registerAndGetUserData().then((user) => {
            cy.getCard().then((card) => {
              cy.log("Привязка карты");
              cy.bindCardToUser(user.token, card.no3DSecureSuccess).then(
                (cardId) => {
                  cy.log("Покупка подписки");
                  cy.buySubscription(monthTariff, user.token, cardId).then(
                    () => {
                      cy.wait(60000).then(() => {
                        cy.log("Проверка активного статуса подписки");
                        cy.checkStatusSubscription(token, 0).then(() => {
                          cy.visit(devRu + "/account/login");
                          cy.get("form");
                          cy.get("input[name='email']").type(user.email, {
                            force: true,
                          });
                          cy.get("input[name='password']").type(password, {
                            force: true,
                          });
                          cy.get("form").submit();
                          cy.wait(5000).then(() => {
                            cy.url().should("include", "/my-courses");
                            for (var url in pagesPersonalCabinetDevRu) {
                              var title = pagesPersonalCabinetDevRu[url];
                              cy.visit(devRu + url);
                              cy.contains(title);
                            }
                          });
                        });
                      });
                    }
                  );
                }
              );
            });
          });
        });
      });
    });

    describe("DEV COM", () => {
      function errorLinksCom() {
        var links = [
          `${devCom}/general/gift-certificates`,
          `${devCom}/account/purchase`,
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
        cy.visit(devCom);
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
          cy.visit(`${devCom}${url}`);
          cy.contains(title);
        }
        cy.url().should("include", "/course/63edce1cbcb16f0028018798/0/0/0");
        cy.get('button[class="afi-button prime"]').click();
        cy.url().should("include", "/subscription");
      });

      it("Проверка доступности страниц в личном кабинете у пользователя с подпиской", () => {
        const pagesPersonalCabinetDevCom = {
          "/catalog": "Catalog",
          //проверку в каталоге добавить на то что курсы отображаются
          "/subscription": "Self-improvement platform",
          "/info/subscriptions": "Paid Subscription usage rules",
          "/account/subscription": "Get unlimited access",
          "/about/about-project": "About the project",
          "/account/profile": "Profile",
          "/my-courses": "Started",
          "/account/bonus": "Gift certificate",
          "/thank-you-page": "New lectures are already in your account!",
          "/subscription/thank-you-page": "Hey! Happy to have you here!",
          //проверка на один курс
          "/course/63edce1cbcb16f0028018798/0/0/0": "Progress",
        };

        cy.getTariff("month").then((monthTariff) => {
          cy.registerAndGetUserData().then((user) => {
            cy.getCard().then((card) => {
              cy.log("Привязка карты");
              cy.bindCardToUser(user.token, card.no3DSecureSuccess).then(
                (cardId) => {
                  cy.log("Покупка подписки");
                  cy.buySubscription(monthTariff, user.token, cardId).then(
                    () => {
                      cy.wait(60000).then(() => {
                        cy.log("Проверка активного статуса подписки");
                        cy.checkStatusSubscription(token, 0).then(() => {
                          cy.visit(devCom + "/account/login");
                          cy.get("form");
                          cy.get("input[name='email']").type(user.email, {
                            force: true,
                          });
                          cy.get("input[name='password']").type(password, {
                            force: true,
                          });
                          cy.get("form").submit();
                          cy.wait(5000).then(() => {
                            cy.url().should("include", "/my-courses");
                            for (var url in pagesPersonalCabinetDevCom) {
                              var title = pagesPersonalCabinetDevCom[url];
                              cy.visit(devCom + url);
                              cy.contains(title);
                            }
                          });
                        });
                      });
                    }
                  );
                }
              );
            });
          });
        });
      });
    });
  }
);

describe(
  "Проверка открытия попапа оплаты подписки на DEV RU и DEV COM",
  {
    retries: 1,
  },
  () => {
    describe("DEV RU", () => {
      const ru1Email =
        "test" + Math.floor(Math.random() * 10000001) + "@forautotest.ru";
      const ru2Email =
        "test" + Math.floor(Math.random() * 10000001) + "@forautotest.ru";

      beforeEach(() => {
        cy.visit(devRu);
        cy.clearCookies();
        cy.clearLocalStorage();
      });

      it("Регистрация пользователя после выбора тарифа", () => {
        cy.visit(devRu + "/subscription");
        //выбор тарифа и регистрация пользователя
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
          cy.contains(
            "Для проверки карты также спишем 11 ₽ и вернем их в течение суток."
          );
        });
      });

      it("Авторизация пользователя после выбора тарифа", () => {
        cy.visit(devRu + "/subscription");
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
          cy.contains(
            "Для проверки карты также спишем 11 ₽ и вернем их в течение суток."
          );
        });
      });

      it("Свежезареганный пользователь", () => {
        cy.visit(devRu + "/account/register");
        cy.contains("Войдите или зарегистрируйтесь");
        cy.get("form");
        cy.get("input[name='email']").type(ru2Email, {
          force: true,
        });
        cy.get("input[name='password']").type(password, { force: true });
        cy.get("form").submit();
        cy.wait(3000).then(() => {
          cy.contains("Ура! Вы успешно зарегистрировались");
          cy.visit(devRu + "/subscription");
          //выбор тарифа
          cy.get("div[class='tariff-block ng-star-inserted']:first").within(
            () => {
              cy.get(
                'button[class="afi-btn afi-primary afi-btn-sm tariff-block-button"]'
              ).click({ force: true });
            }
          );
          cy.log("Открытие попапа для оплаты");
          cy.contains(
            "Для проверки карты также спишем 11 ₽ и вернем их в течение суток."
          );
        });
      });

      it("Открытие тарифов уже авторизованным пользователем", () => {
        cy.visit(devRu + "/account/login");
        cy.contains("Войдите или зарегистрируйтесь");
        cy.get("form");
        cy.get("input[name='email']").type(ru2Email, {
          force: true,
        });
        cy.get("input[name='password']").type(password, { force: true });
        cy.get("form").submit();
        cy.wait(3000).then(() => {
          cy.url().should("include", "/my-courses");
          cy.visit(devRu + "/subscription");
          //выбор тарифа
          cy.get("div[class='tariff-block ng-star-inserted']:first").within(
            () => {
              cy.get(
                'button[class="afi-btn afi-primary afi-btn-sm tariff-block-button"]'
              ).click({ force: true });
            }
          );
          cy.log("Открытие попапа для оплаты");
          cy.contains(
            "Для проверки карты также спишем 11 ₽ и вернем их в течение суток."
          );
        });
      });
    });

    describe("DEV COM", () => {
      const com1Email =
        "test" + Math.floor(Math.random() * 10000001) + "@forautotest.ru";
      const com2Email =
        "test" + Math.floor(Math.random() * 10000001) + "@forautotest.ru";

      beforeEach(() => {
        cy.visit(devCom);
        cy.clearCookies();
        cy.clearLocalStorage();
      });

      /*it("Регистрация пользователя после выбора тарифа", () => {
        cy.visit(devCom + "/subscription");
        //выбор тарифа и регистрация пользователя
        cy.get("div[class='tariff-block ng-star-inserted']:first").within(
          () => {
            cy.get(
              'button[class="afi-btn afi-primary afi-btn-sm tariff-block-button"]'
            ).click({ force: true });
          }
        );
        cy.contains("Log in or sign up");
        cy.contains("Log in").click()
        cy.contains("Forgot your password?")
        cy.get("form");
        cy.get("input[name='email']").type(com1Email, {
          force: true,
        });
        cy.get("input[name='password']").type(password, { force: true });
        cy.get("form").submit();
        cy.wait(5000).then(() => {
          cy.log("Открытие попапа для оплаты");
          cy.contains(
            "To complete the subscription, you need to provide your card information."
          );
        });
      });

      it("Авторизация пользователя после выбора тарифа", () => {
        cy.visit(devCom + "/subscription");
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
          cy.contains(
            "To complete the subscription, you need to provide your card information."
          );
        });
      });*/

      it("Свежезареганный пользователь", () => {
        cy.visit(devCom + "/account/register");
        cy.contains("Log in or sign up");
        cy.get("form");
        cy.get("input[name='email']").type(com2Email, {
          force: true,
        });
        cy.get("input[name='password']").type(password, { force: true });
        cy.get("form").submit();
        cy.wait(3000).then(() => {
          cy.contains("Hurrah! You have successfully registered");
          cy.visit(devCom + "/subscription");
          //выбор тарифа
          cy.get("div[class='tariff-block ng-star-inserted']:first").within(
            () => {
              cy.get(
                'button[class="afi-btn afi-primary afi-btn-sm tariff-block-button"]'
              ).click({ force: true });
            }
          );
          cy.log("Открытие попапа для оплаты");
          cy.contains(
            "To complete the subscription, you need to provide your card information."
          );
        });
      });

      it("Открытие тарифов уже авторизованным пользователем", () => {
        cy.visit(devCom + "/account/login");
        cy.contains("Log in or sign up");
        cy.get("form");
        cy.get("input[name='email']").type(com2Email, {
          force: true,
        });
        cy.get("input[name='password']").type(password, { force: true });
        cy.get("form").submit();
        cy.wait(3000).then(() => {
          cy.url().should("include", "/my-courses");
          cy.visit(devCom + "/subscription");
          //выбор тарифа
          cy.get("div[class='tariff-block ng-star-inserted']:first").within(
            () => {
              cy.get(
                'button[class="afi-btn afi-primary afi-btn-sm tariff-block-button"]'
              ).click({ force: true });
            }
          );
          cy.log("Открытие попапа для оплаты");
          cy.contains(
            "To complete the subscription, you need to provide your card information."
          );
        });
      });
    });
  }
);
