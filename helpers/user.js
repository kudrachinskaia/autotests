const apiNew = Cypress.env("apiNew");
const password = "Password1"; //стандартный пароль, присваивающийся всем пользователям

// Регистрирует пользователя и возвращает информацию о нем
Cypress.Commands.add("registerAndGetUserData", () => {
  const email =
    "test+" + Math.floor(Math.random() * 10000001) + "@getnada.com";
  cy.getDeviceId().then((deviceId) => {
    cy.request({
      method: "POST",
      url: `${apiNew}/auth/register`,
      headers: {
        "x-device-uid": deviceId,
      },
      body: {
        email: email,
        password: password,
      },
    }).then((response) => {
      expect(response).to.have.property("status", 200);
      expect(response.body.data).to.have.property("token");
      response = {
        token: response.body.data["token"],
        email: email,
      };
      return response;
    });
  });
});

//Сбрасывает пароль пользователя и присваивает стандартный 11111
Cypress.Commands.add("updateUserPassword", (email) => {
  cy.updateOne(
    { email: email },
    {
      $set: {
        pwdHash:
          "d17f25ecfbcc7857f7bebea469308be0b2580943e96d13a3ad98a13675c4bfc2",
      },
    },
    { collection: "users", database: "afi-dev" }
  );
});

//сбрасывает старые девайсы пользователя, присваивает новый один и возвращает его
Cypress.Commands.add("updateUserDeviceId", (email) => {
  cy.getDeviceId().then((newDevice) => {
    const currentDate = new Date().toISOString();
    cy.findOne(
      { email: email },
      { collection: "users", database: "afi-dev" }
    ).then(() => {
      cy.updateOne(
        { email: email },
        {
          $set: {
            devices: [
              {
                deviceId: newDevice,
                attachedOn: currentDate,
                isActive: true,
                platform: 0,
                client: 0,
                seed: "1672059541.839218", //что это?}
              },
            ],
          },
        },
        { collection: "users", database: "afi-dev" }
      ).then(() => {
        cy.findOne(
          { email: email },
          { collection: "users", database: "afi-dev" }
        ).then((result) => {
          expect(result.devices[0]).to.have.property("deviceId", newDevice);
          return newDevice;
        });
      });
    });
  });
});

//Получение карты пользователя
Cypress.Commands.add("getUserCardId", (accessToken) => {
  cy.request({
    method: "GET",
    url: apiNew + "/credit-cards",
    headers: {
      authorization: `Bearer ${accessToken}`,
    },
  }).then((response) => {
    expect(response).to.have.property("status", 200);
    const cardId = response.body.data.cards[0].id;
    return cardId;
  });
});

/* Отдает одно из значений x-device-uid
  Iphone 13: 384078453, 4245410370, 1951898248
  Iphone 13 Pro: 812999805, 812999805
  Iphone 13 Pro Max: 1502315250
  Iphone 12 Pro: 3217883995
  MAC: 98014819, 694018251
  хром: 3691908401
  //декстопные добавить и андройд
  */
Cypress.Commands.add("getDeviceId", () => {
  const deviceIds = [
    384078453, 4245410370, 1951898248, 812999805, 812999805, 1502315250,
    3217883995, 98014819, 694018251, 3691908401,
  ];
  const randomIndex = Math.floor(Math.random() * deviceIds.length);
  return deviceIds[randomIndex];
});

//Привязка карты юзеру
Cypress.Commands.add("bindCardToUser", (token, card) => {
  cy.getCriptogramm(card).then((criptogramm) => {
    cy.getCardExpireDate().then((expireDate) => {
      cy.getFirstAndLastNumberCard(card).then((cardNumbers) => {
        cy.request({
          method: "POST",
          url: `${apiNew}/credit-cards/cp`,
          headers: {
            authorization: `Bearer ${token}`,
          },
          body: {
            cardCryptogramPacket: criptogramm,
            cardExpireDate: expireDate,
            firstSix: cardNumbers.first,
            lastFour: cardNumbers.last,
          },
        }).then((response) => {
          expect(response).to.have.property("status", 200);
          expect(response.body.data).to.have.property("cardId");
          return response.body.data.cardId;
        });
      });
    });
  });
});

//Проверка привязанных карт
Cypress.Commands.add("checkBindedCard", (token, cardId, numbers) => {
  cy.request({
    method: "GET",
    url: `${apiNew}/credit-cards`,
    headers: {
      authorization: `Bearer ${token}`,
    },
  }).then((response) => {
    expect(response.status).to.eq(200);
    expect(response.body.data.cards).to.be.an("array").that.is.not.empty;
    const boundCard = response.body.data.cards.find((c) => c.id === cardId);
    expect(boundCard).to.not.be.undefined;
    expect(boundCard.cardNumber.firstSix).to.eq(numbers.first);
    expect(boundCard.cardNumber.lastFour).to.eq(numbers.last);
    expect(boundCard.main).to.be.true;
  });
});

//Проверка, что у подписки нужный статус
Cypress.Commands.add("checkStatusSubscription", (accessToken, status) => {
  cy.request({
    method: "GET",
    url: `${apiNew}/subscriptions`,
    headers: {
      authorization: `Bearer ${accessToken}`,
    },
  }).then((response) => {
    expect(response.status).to.equal(200);
    expect(response.body.data.status).to.equal(status);
  });
});

//Покупка подписки с нужным тариффом и проверка ее статуса 0
Cypress.Commands.add("buySubscription", (tariff, token, cardId) => {
  cy.request({
    method: "POST",
    url: `${apiNew}/subscriptions`,
    headers: {
      authorization: `Bearer ${token}`,
    },
    body: {
      creditCardId: cardId,
      tariffId: tariff,
    },
  }).then((response) => {
    expect(response).to.have.property("status", 200);
    cy.wait(60000).then(() => {
      cy.log("Проверка активного статуса подписки");
      cy.checkStatusSubscription(token, 0).then(() => {});
    });
  });
});

//Регистрация и покупка подписки с реф айди
Cypress.Commands.add("buySubscriptionWithRefferId", (tariff, refId) => {
  cy.log("Регистрация пользователя");
  cy.registerAndGetUserData().then((user) => {
    cy.getCard().then((card) => {
      cy.log("Привязка карты");
      cy.bindCardToUser(user.token, card.no3DSecureSuccess).then((cardId) => {
        cy.getTariff(tariff).then((tariff) => {
          cy.log("Покупка подписки с ref id");
          cy.request({
            method: "POST",
            url: `${apiNew}/subscriptions`,
            headers: {
              authorization: `Bearer ${user.token}`,
            },
            body: {
              creditCardId: cardId,
              tariffId: tariff,
              referrerId: refId,
            },
          }).then((response) => {
            expect(response).to.have.property("status", 200);
            cy.wait(60000).then(() => {
              cy.log("Проверка активного статуса подписки");
              cy.checkStatusSubscription(user.token, 0).then(() => {
                const data = {
                  token: user.token,
                  email: user.email,
                };
                return data;
              });
            });
          });
        });
      });
    });
  });
});

//Создание реферера, покупка ему подписки и возврат его инфо
Cypress.Commands.add("createReferer", (tariffPeriod) => {
  cy.log("Регистрация реферера");
  cy.registerAndGetUserData().then((refferer) => {
    cy.getCard().then((card) => {
      cy.log("Привязка карты");
      cy.bindCardToUser(refferer.token, card.no3DSecureSuccess).then((cardId) => {
        cy.getTariff(tariffPeriod).then((tariffId) => {
          cy.log("Покупка подписки");
          cy.buySubscription(tariffId, refferer.token, cardId).then(() => {
            cy.log("Получение реф id");
            cy.getReferrerId(refferer.token).then((refId) => {
              cy.getExternalId(refferer.email).then((externalId) => {
                var refererInfo = {
                  refId: refId,
                  externalId: externalId,
                };
                return refererInfo;
              });
            });
          });
        });
      });
    });
  });
});

//Получение refid
Cypress.Commands.add("getReferrerId", (token) => {
  cy.request({
    method: "GET",
    url: `${apiNew}/users/me`,
    headers: {
      authorization: `Bearer ${token}`,
    },
  }).then((response) => {
    expect(response.body.data).to.have.property("refid");
    return response.body.data.refid;
  });
});
