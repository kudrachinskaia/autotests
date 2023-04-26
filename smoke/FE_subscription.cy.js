const apiNew = Cypress.env("apiNew");
const devRu = Cypress.env("devRu");

const month = "month";
const year = "year";
const twoYears = "forever";
const password = "Password1";

it("Покупка подписки не авторизованным пользователем", () => {
  cy.visit(devRu);
  cy.clearCookies();
  cy.clearLocalStorage().then(() => {
    //получение карты для покупки подписки
    cy.getCard().then((card) => {
      cy.getCardExpireDate().then((expireDate) => {
        const cvv = Math.floor(Math.random() * 100 + 100);

        //регистрация пользователя
        cy.visit(`${devRu}/subscription`);
        cy.get("div[class='tariff-block ng-star-inserted']:first").within(
          () => {
            cy.get(
              'button[class="afi-btn afi-primary afi-btn-sm tariff-block-button"]'
            ).click({ force: true });
          }
        );
        cy.contains("Войдите или зарегистрируйтесь");
        cy.get("form");
        var email =
          "testart+" + Math.floor(Math.random() * 10000001) + "@getnada.com";
        cy.get("input[name='email']").type(email, {
          force: true,
        });
        cy.get("input[name='password']").type(password, { force: true });
        cy.get("form")
          .submit()
          .then(() => {
            //получение токена пользователя
            cy.wait(5000).then(() => {
              var accessToken = localStorage.getItem("TKN-AFI");
              cy.log(accessToken);

              cy.log("Ввод данных карты и переход к оплате");
              cy.contains(
                "Для проверки карты также спишем 11 ₽ и вернем их в течение суток."
              );
              cy.get("form");
              cy.get("input[name='number']").type(card.no3DSecureSuccess);
              cy.get("input[name='expired']").type(expireDate);
              cy.get("input[name='cvv']").type(cvv);
              cy.get(
                'button[class="afi-btn afi-primary afi-btn-sm afi-loading btn-with-out-padding"]'
              )
                .click()
                .wait(4000)
                .then(() => {
                  cy.contains("Подписка оформлена!");
                  cy.wait(2000).then(() => {
                    cy.contains("Станьте лучше");
                    cy.url().should("include", "/onboarding");
                    cy.wait(50000).then(() => {
                      cy.checkStatusSubscription(accessToken, 0).then(() => {});
                    });
                  });
                });
            });
          });
      });
    });
  });
});

//руками нужно проходить проверку на то что не робот
it("Покупка подписки с UTM-метками", () => {
  cy.visit(devRu);
  cy.clearCookies();
  cy.clearLocalStorage().then(() => {
    cy.getCard().then((card) => {
      cy.getCardExpireDate().then((expireDate) => {
        const cvv = Math.floor(Math.random() * 100 + 100);
        const utms = [
          "https://clck.ru/34B2Bp",
          "https://is.gd/J77lgB",
          "https://is.gd/3osrnm",
          "https://clck.ru/34B2Bp",
        ];
        var someUtm = Math.floor(Math.random() * utms.length);

        //переход по ссылке с utm-метками
        cy.visit(utms[someUtm]);

        //выбор тарифа и регистрация пользователя
        var email =
          "testart+" + Math.floor(Math.random() * 10000001) + "@getnada.com";
        cy.get("div[class='tariff-block ng-star-inserted']:first").within(
          () => {
            cy.get(
              'button[class="afi-btn afi-primary afi-btn-sm tariff-block-button"]'
            ).click({ force: true });
          }
        );
        cy.contains("Войдите или зарегистрируйтесь");
        cy.get("form");
        cy.get("input[name='email']").type(email, {
          force: true,
        });
        cy.get("input[name='password']").type(password, { force: true });
        cy.get("form")
          .submit()
          .then(() => {
            //получение токена пользователя
            cy.wait(10000).then(() => {
              var accessToken = localStorage.getItem("TKN-AFI");

              cy.log("Ввод данных карты и переход к оплате");
              cy.contains(
                "Для проверки карты также спишем 11 ₽ и вернем их в течение суток."
              );
              cy.get("form");
              cy.get("input[name='number']").type(card.no3DSecureSuccess);
              cy.get("input[name='expired']").type(expireDate);
              cy.get("input[name='cvv']").type(cvv);
              cy.get(
                'button[class="afi-btn afi-primary afi-btn-sm afi-loading btn-with-out-padding"]'
              )
                .click()
                .wait(4000)
                .then(() => {
                  cy.contains("Станьте лучше");
                  cy.url().should("include", "/onboarding");
                  cy.wait(50000).then(() => {
                    cy.checkStatusSubscription(accessToken, 0).then(() => {});
                  });
                });
            });
          });
      });
    });
  });
});
