const apiNew = Cypress.env("apiNew");
const password = Cypress.env("password");

//Регистрация пользователя. Возврат email и token
Cypress.Commands.add("registerAndGetUserData", () => {
  const email =
    "test" + Math.floor(Math.random() * 1000000001) + "@forautotest.ru";
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

//Получение userId пользователя
Cypress.Commands.add("getUserId", (accessToken) => {
  cy.request({
    method: "GET",
    url: `${apiNew}/users/me`,
    headers: {
      authorization: `Bearer ${accessToken}`,
    },
  }).then((response) => {
    expect(response).to.have.property("status", 200);
    return response.body.data.id;
  });
});

//Получение токена пользователя
Cypress.Commands.add("getUserToken", (email, password) => {
  cy.getDeviceId().then((deviceId) => {
    return cy
      .request({
        method: "POST",
        url: `${apiNew}/auth/login`,
        headers: {
          "x-device-uid": deviceId,
        },
        body: {
          email: email,
          password: password,
        },
      })
      .then((response) => {
        return response.body.data.token;
      });
  });
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
    url: `${apiNew}/credit-cards`,
    headers: {
      authorization: `Bearer ${accessToken}`,
    },
  }).then((response) => {
    expect(response).to.have.property("status", 200);
    const cardId = response.body.data.cards[0].id;
    return cardId;
  });
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

//Проверка привязанных карт у пользователя
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

//проверка отображения результатов онбординга у пользователя
Cypress.Commands.add(
  "checkOnboardingResults",
  (token, categoryIds, goalsIds, courseIds) => {
    cy.request({
      method: "GET",
      url: `${apiNew}/users/me`,
      headers: {
        authorization: `Bearer ${token}`,
      },
    }).then((response) => {
      expect(response).to.have.property("status", 200);
      expect(response.body.data.onboarding.categories).to.deep.equal(
        categoryIds
      );
      expect(response.body.data.onboarding.goals).to.deep.equal(goalsIds);
      expect(response.body.data.onboarding.courses).to.deep.equal(courseIds);
    });
  }
);

//Сброс кэша пользователю в редисе
Cypress.Commands.add("clearRedisCache", (token) => {
  cy.getUserId(token).then((refererUserId) => {
    cy.log("Сброс кэша для реферера с ID:", refererUserId);
    cy.exec(
      `redis-cli -h redis-main.infra-develop.svc.cluster.local del users:${refererUserId}`
    );
  });
});

//Проверка привязанных рефералов у пользователя
Cypress.Commands.add(
  "checkRefferals",
  (referalUserId, reffererToken, index) => {
    cy.request({
      method: "GET",
      url: `${apiNew}/users/me`,
      headers: {
        authorization: `Bearer ${reffererToken}`,
      },
    }).then((response) => {
      expect(response.body.data.referrals[index]).to.have.property(
        "id",
        referalUserId
      );
    });
  }
);

//Сравнение External Ids
Cypress.Commands.add(
  "comparisonExternalId",
  (firstExternalId, secondExternalId) => {
    if (firstExternalId === secondExternalId) {
      throw new Error("External ID не изменился");
    }
  }
);
