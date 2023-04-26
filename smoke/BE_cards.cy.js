const apiNew = Cypress.env("apiNew");

it("Привязка карты пользователю", () => {
  cy.registerAndGetUserData().then((user) => {
    cy.getCard().then((card) => {
      var currentCard = card.no3DSecureSuccess;
      cy.getFirstAndLastNumberCard(currentCard).then((numbers) => {
        cy.bindCardToUser(user.token, currentCard).then((cardId) => {
          cy.checkBindedCard(user.token, cardId, numbers);
          //проверка что у карты mein - true
        });
      });
    });
  });
});

it("Negative - Привязка карты с нулевым балансом", () => {
  cy.registerAndGetUserData().then((user) => {
    cy.getCard().then((card) => {
      var currentCard = card.no3DSecureUnsuccess;
      cy.getCriptogramm(currentCard).then((criptogramm) => {
        cy.getCardExpireDate().then((expireDate) => {
          cy.getFirstAndLastNumberCard(currentCard).then((cardNumbers) => {
            cy.request({
              method: "POST",
              url: `${apiNew}/credit-cards/cp`,
              headers: {
                authorization: `Bearer ${user.token}`,
              },
              body: {
                cardCryptogramPacket: criptogramm,
                cardExpireDate: expireDate,
                firstSix: cardNumbers.first,
                lastFour: cardNumbers.last,
              },
              failOnStatusCode: false,
            }).then((response) => {
              expect(response).to.have.property("status", 500);
              cy.log("Проверка что у пользователя нет привязанной карты");
              cy.request({
                method: "GET",
                url: `${apiNew}/credit-cards`,
                headers: {
                  authorization: `Bearer ${user.token}`,
                },
              }).then((response) => {
                expect(response).to.have.property("status", 200);
                expect(response.body.data).to.have.property("cards");
                expect(response.body.data.cards).to.be.empty;
              });
            });
          });
        });
      });
    });
  });
});
