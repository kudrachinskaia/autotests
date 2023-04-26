const apiNew = Cypress.env("apiNew");
const devRu = Cypress.env("devRu");

const month = "month";
const year = "year";
const twoYears = "forever";

describe("Покупка подписок с каждым из тарифов", () => {
  function buySubscriptions(tariff) {
    cy.log("Регистрация пользователя");
    cy.registerAndGetUserData().then((user) => {
      cy.getCard().then((card) => {
        cy.log("Привязка карты");
        cy.bindCardToUser(user.token, card.no3DSecureSuccess).then((cardId) => {
          cy.getTariff(tariff).then((tariffId) => {
            cy.log("Покупка подписки");
            cy.buySubscription(tariffId, user.token, cardId).then(() => {});
          });
        });
      });
    });
  }
  it("Покупка подписки на месяц пользователем", () => {
    buySubscriptions(month);
  });

  it("Покупка подписки на год пользователем", () => {
    buySubscriptions(year);
  });

  it("Покупка подписки на 2 года пользователем", () => {
    buySubscriptions(twoYears);
  });
});

describe("Update подписки по каждому из тарифов", () => {
  function updateSubscription(firstTariff, secondTariff) {
    cy.log("Регистрация пользователя");
    cy.registerAndGetUserData().then((user) => {
      cy.getCard().then((card) => {
        cy.log("Привязка карты");
        cy.bindCardToUser(user.token, card.no3DSecureSuccess).then((cardId) => {
          cy.getTariff(firstTariff).then((firstIdTariff) => {
            cy.log("Покупка подписки");
            cy.buySubscription(firstIdTariff, user.token, cardId).then(() => {
              cy.getExternalId(user.email).then((firstExternalId) => {
                cy.getTariff(secondTariff).then((secondIdTariff) => {
                  cy.log("Смена тарифа подписки");
                  cy.request({
                    method: "POST",
                    url: `${apiNew}/subscriptions/update`,
                    headers: {
                      authorization: `Bearer ${user.token}`,
                    },
                    body: {
                      creditCardId: cardId,
                      tariffId: secondIdTariff,
                    },
                  }).then((response) => {
                    expect(response).to.have.property("status", 200);
                    cy.wait(60000).then(() => {
                      cy.log("Проверка активного статуса подписки");
                      cy.checkStatusSubscription(user.token, 0);
                      cy.log("Сравнение старого и нового External Id");
                      cy.getExternalId(user.email).then((secondExternalId) => {
                        if (firstExternalId === secondExternalId) {
                          throw new Error("External ID не изменился");
                        }
                      });
                    });
                  });
                });
              });
            });
          });
        });
      });
    });
  }
  it("Переход с месячной подписки на годовую", () => {
    updateSubscription(month, year);
  });

  it("Переход с месячного тарифа на 2-х летний", () => {
    updateSubscription(month, twoYears);
  });

  it("Переход с годового тарифа на 2-х летний", () => {
    updateSubscription(year, twoYears);
  });

  it("Переход с годового тарифа на месячный", () => {
    updateSubscription(year, month);
  });

  it("Переход с 2-х летнего тарифа на месячный", () => {
    updateSubscription(twoYears, month);
  });

  it("Переход с 2-х летнего тарифа на годовой", () => {
    updateSubscription(twoYears, year);
  });
});

it("Negative - Покупка подписки с таким же тарифом пользователем с имеющейся активной подпиской", () => {
  cy.log("Регистрация пользователя");
  cy.registerAndGetUserData().then((user) => {
    cy.getCard().then((card) => {
      cy.log("Привязка карты");
      cy.bindCardToUser(user.token, card.no3DSecureSuccess).then((cardId) => {
        cy.getTariff(month).then((monthTariff) => {
          cy.log("Покупка подписки");
          cy.buySubscription(monthTariff, user.token, cardId).then(() => {
            cy.log("Попытка покупки еще одной подписки на месяц пользователем");
            cy.request({
              method: "POST",
              url: `${apiNew}/subscriptions`,
              headers: {
                authorization: `Bearer ${user.token}`,
              },
              body: {
                creditCardId: cardId,
                tariffId: monthTariff,
              },
              failOnStatusCode: false,
            }).then((response) => {
              expect(response).to.have.property("status", 500);
              //проверить что экстернал ид не изменился?
            });
          });
        });
      });
    });
  });
});

it("Отмена подписки", () => {
  cy.log("Регистрация пользователя");
  cy.registerAndGetUserData().then((user) => {
    cy.getCard().then((card) => {
      cy.log("Привязка карты");
      cy.bindCardToUser(user.token, card.no3DSecureSuccess).then((cardId) => {
        cy.getTariff(month).then((monthTariff) => {
          cy.log("Покупка подписки");
          cy.buySubscription(monthTariff, user.token, cardId).then(() => {
            cy.log("Отмена подписки");
            cy.request({
              method: "POST",
              url: `${apiNew}/subscriptions/cancel`,
              headers: { authorization: `Bearer ${user.token}` },
            }).then((response) => {
              expect(response).to.have.property("status", 200);
              cy.wait(60000).then(() => {
                cy.log("Проверка отмененного статуса подписки");
                cy.checkStatusSubscription(user.token, 3).then(() => {});
              });
            });
          });
        });
      });
    });
  });
});
