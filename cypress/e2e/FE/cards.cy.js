//не актуально

const devRu = Cypress.env("devRu");

const email = "test" + Math.floor(Math.random() * 10000001) + "@getnada.com";
const password = Cypress.env("password");

it("Успешная покупка подписки картой с 3D secure", () => {
  cy.visit(devRu);
  cy.clearCookies();
  cy.clearLocalStorage().then(() => {
    //получение карты
    cy.getCard().then((cardNumber) => {
      cy.getCardExpireDate().then((expaireDate) => {
        const cvv = Math.floor(Math.random() * 100 + 100);

        //выбор тарифа и регистрация пользователя
        cy.visit(`${devRu}/subscription`);
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
        cy.get("input[name='email']").type(email, {
          force: true,
        });
        cy.get("input[name='password']").type(password, { force: true });
        cy.get("form").submit();
        cy.wait(5000).then(() => {
          cy.log("Ввод данных карты и переход к оплате");
          cy.contains(
            "Для проверки карты также спишем 11 ₽ и вернем их в течение суток."
          );
          cy.get("form");
          cy.get("input[name='number']").type(cardNumber.with3DSecureSuccess);
          cy.get("input[name='expired']").type(expaireDate);
          cy.get("input[name='cvv']").type(cvv);
          cy.get(
            'button[class="afi-btn afi-primary afi-btn-sm afi-loading btn-with-out-padding"]'
          ).click();
          cy.origin("https://acs.cloudpayments.ru/", () => {
            cy.contains(
              "Это тестовая аутентификация 3D Secure, для транзакции с демо-стенда."
            );
            cy.contains("Успех").click();
          });
          cy.contains("Подписка оформлена!");
          cy.wait(50000).then(() => {
            cy.checkActiveSubscription(email).then(() => {});
          });
        });
      });
    });
  });
});

it("НЕуспешная покупка подписки картой с 3D secure", () => {
  const email =
    "testart+" + Math.floor(Math.random() * 10000001) + "@getnada.com";
  cy.visit(devRu);
  cy.getCookies();
  cy.clearCookies();
  cy.getLocalStorage;
  cy.clearLocalStorage().then(() => {
    cy.visit(`${devRu}/account/register`);
    cy.get("form");
    cy.get("input[name='email']").type(email, {
      force: true,
    });
    cy.get("input[name='password']").type("11111", { force: true });
    cy.get("form").submit();
    cy.wait(2000).then(() => {
      cy.contains("Ура! Вы успешно зарегистрировались");
      cy.log("Покупка подписки");
      cy.getCard().then((cardNumber) => {
        cy.getCardExpireDate().then((expaireDate) => {
          const cvv = Math.floor(Math.random() * 100 + 100);
          cy.visit(`${devRu}/subscription`);
          cy.get("div[class='tariff-block ng-star-inserted']:first").within(
            () => {
              cy.get(
                'button[class="afi-btn afi-primary afi-btn-sm tariff-block-button"]'
              ).click({ force: true });
            }
          );
          cy.log("Ввод данных карты и переход к оплате");
          cy.get("form");
          cy.get("input[name='number']").type(cardNumber.with3DSecureSuccess);
          cy.get("input[name='expired']").type(expaireDate);
          cy.get("input[name='cvv']").type(cvv);
          cy.get(
            'button[class="afi-btn afi-primary afi-btn-sm afi-loading btn-with-out-padding"]'
          ).click();
          cy.origin("https://acs.cloudpayments.ru/", () => {
            cy.contains(
              "Это тестовая аутентификация 3D Secure, для транзакции с демо-стенда."
            );
            cy.contains("Неудача").click();
          });
          cy.contains("Аутентификация 3D Secure");
          cy.wait(50000).then(() => {
            cy.getUserData(email, "694018251").then((user) => {
              //проверка отстуствия подписки
              cy.request({
                method: "GET",
                url: "/api/v3/subscriptions",
                headers: {
                  authorization: "Bearer " + user.accessToken,
                },
                failOnStatusCode: false,
              }).then((response) => {
                expect(response).to.have.property("status", 404);
                expect(response.body).to.have.property(
                  "message",
                  "Подписка не найдена."
                );
              });
            });
          });
        });
      });
    });
  });
});
