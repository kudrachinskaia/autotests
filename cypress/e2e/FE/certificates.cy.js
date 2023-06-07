//не актуально пока нет админки

const devRu = Cypress.env("devRu");

/*it("Покупка сертификата без авторизации", () => {
  cy.visit(devRu);
  cy.clearCookies();
  cy.clearLocalStorage().then(() => {

    const cost = 5000; //Math.floor(Math.random() * 6001);
    const costWith20 = (cost / 100) * 20 + cost;
    //const first = Math.floor(cost / 1e3);
    // const last = cost % 1e3;
    // const price = first + " " + last;

    cy.visit(`${devRu}/gift-certificates`);
    cy.get('label[nzvalue="another"]').click({ force: true });
    cy.focused().clear().type(cost);
    cy.contains("Оплатить").click({ force: true });
    cy.contains("Подарочный сертификат на сумму " + costWith20 + " ₽");
    cy.contains(
      "Внимание! Введите корректный e-mail, на него придет ваш сертификат"
    );
    cy.contains("Сумма к оплате: 5 000 ₽");
    cy.get('input[formcontrolname="email"]').type(
      "testart+" + Math.floor(Math.random() * 10001) + "@getnada.com"
    );
    cy.get('button[form="getAccessToken"]').contains("Купить").click({ force: true });
    cy.wait(10000).then(() => {
      cy.getCard().then((cardNumber) => {
        cy.getCardExpireDate().then((expaireDate) => {
          const cvv = Math.floor(Math.random() * 100 + 100);

          //cy.contains("Оплата товаров")
          cy.get('div[class="app widget"]');
          cy.get('input[name="card"]').type(cardNumber.no3DSecureSuccess);
          cy.get("input[id='date']").type(expaireDate);
          cy.get("input[id='cvv']").type(cvv);
          cy.get(
            'button[class="afi-btn afi-primary afi-btn-sm afi-loading btn-with-out-padding"]'
          ).click();
          cy.contains("Платеж завершён");
          cy.contains("Оплата товаров 3 013 ₽");
          //чек что отправилась ссылка
        });
      });
    });
  });
});*/


it("Активация сертификата полученного от администратора", () => {
  //на бэке получить серт из админки - Request URL: https://dev.artforintrovert.ru:3000/certificates/create
  //зарегать пользователя и взять токен
  //перейти в лк с установленными куки
  cy.visit("https://dev.artforintrovert.ru:1180");
  cy.get("form");
  cy.get('input[type="text"]').type("natali", {
    force: true,
  });
  cy.get("input[type='password']").type("PonUHdHe", { force: true });
  cy.get("form").submit();
  cy.contains("Сертификаты").click();
  cy.contains("Создать новые сертификаты").click();
  cy.contains("Доступен для активации");
  cy.get("input").first().type("5000");
  cy.get("input").eq(3).type("1");
  cy.contains("Сохранить").click();
  cy.contains("Скопировать").click();
  cy.window()
    .its("navigator.clipboard")
    .invoke("readText")
    .then((promocode) => {
      cy.visit(devRu);
      cy.getCookies();
      cy.clearCookies();
      cy.getLocalStorage;
      cy.clearLocalStorage().then(() => {
        cy.visit(`${devRu}/account/register`);
        const email =
          "testart+" + Math.floor(Math.random() * 10000001) + "@getnada.com";
        cy.get("form");
        cy.get("input[name='email']").type(email, {
          force: true,
        });
        cy.get("input[name='password']").type("11111", { force: true });
        cy.get("form").submit();
        cy.wait(2000).then(() => {
          cy.contains("Ура! Вы успешно зарегистрировались");

          cy.visit(`${devRu}/account/bonus`);
          cy.contains("Мои сертификаты");
          cy.get('input[formcontrolname="activateCode"]').type(promocode, {
            force: true,
          });
          cy.contains("Активировать").click({ force: true });
          cy.contains("Баланс пополнен");
          cy.visit(`${devRu}/account/profile`);
          cy.contains("Мои бонусы");
          cy.contains("5 000 бонусов");
        });
      });
    });
});

/*it("Активация сертификата полученного в подарок", () => {
  //генериить через админа?
  const email =
    "testart+" + Math.floor(Math.random() * 10000001) + "@getnada.com";
  cy.visit(`${devRu}/account/login`);
  cy.getCookies();
  cy.clearCookies();
  cy.getLocalStorage;
  cy.clearLocalStorage().then(() => {
    cy.get("form");
    cy.get("input[name='email']").type(email, {
      force: true,
    });
    cy.get("input[name='password']").type("11111", { force: true });
    cy.get("form").submit();
    cy.wait(2000).then(() => {
      cy.url().should("include", "account/purchase");
      cy.contains("Все покупки");

      cy.visit(`${devRu}/account/bonus`);
      cy.contains("Мои сертификаты");
    });
  });
});
*/
/*it("Покупка сертификата авторизованным пользователем", () => {
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
      cy.visit(`${devRu}/gift-certificates`);
      const cost = 5000;
      const costWith20 = (cost / 100) * 20 + cost;
      cy.get('label[nzvalue="another"]').click({ force: true });
      cy.focused().clear().type(cost);
      cy.contains("Оплатить").click({ force: true });
      cy.contains("Подарочный сертификат на сумму " + costWith20 + " ₽");
      cy.contains("Сумма к оплате: 5 000 ₽");
      cy.get('button[form="getAccessToken"]').contains("Купить").click({ force: true });
      cy.wait(10000).then(() => {
        cy.getCard().then((cardNumber) => {
          cy.getCardExpireDate().then((expaireDate) => {
            const cvv = Math.floor(Math.random() * 100 + 100);

            cy.get("form");
            cy.get("input[name='number']").type(
              cardNumber.with3DSecureSuccsess
            );
            cy.get("input[name='expired']").type(expaireDate);
            cy.get("input[name='cvv']").type(cvv);
            cy.get(
              'button[class="afi-btn afi-primary afi-btn-sm afi-loading btn-with-out-padding"]'
            ).click();

            cy.contains("Платеж завершён");
            cy.contains("Оплата товаров 3 013 ₽");

            //чек что отправилась ссылка
          });
        });
      });
    });
  });
});
*/
