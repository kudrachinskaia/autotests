const apiNew = Cypress.env("apiNew");

const monthTariff = "month";

//Активация подписки
Cypress.Commands.add("actiovationSubscription", (email) => {
  cy.findOne(
    { email: email },
    { collection: "users", database: "afi-dev" }
  ).then((user) => {
    if (!user) {
      throw new Error(`Пользователь с этим email ${email} не найден`);
    }
    const userId = user._id;
    cy.findOne(
      { ownerId: userId },
      { collection: "subscriptions_v2", database: "afi-dev" }
    ).then((subscription) => {
      cy.updateOne(
        { _id: subscription._id },
        { $set: { status: 0 } },
        { collection: "subscriptions_v2", database: "afi-dev" }
      ).then(() => {
        cy.log(`Статус подписки с id ${subscription._id} успешно обновлен.`);
      });
    });
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

//Покупка подписки с нужным тариффом
Cypress.Commands.add("buySubscription", (tariff, token, cardId, refId) => {
  cy.request({
    method: "POST",
    url: `${apiNew}/subscriptions`,
    headers: {
      authorization: `Bearer ${token}`,
    },
    body: {
      creditCardId: cardId,
      tariffId: tariff,
      referrerId: refId,
    },
  }).then((response) => {
    expect(response).to.have.property("status", 200);
  });
});

//Создание пользователя и покупка подписки c/без refid, возврат refId, externalId, token, email
Cypress.Commands.add("createUserWithSubscription", (tariffPeriod, refId) => {
  let waitTime = 130000;
  cy.log("Регистрация пользователя");
  cy.registerAndGetUserData().then((user) => {
    cy.getCard().then((card) => {
      cy.log("Привязка карты");
      cy.bindCardToUser(user.token, card.no3DSecureSuccess).then((cardId) => {
        cy.getTariff(tariffPeriod).then((tariffId) => {
          cy.log("Покупка подписки");
          //если refId передан(Покупка подписки рефералу с refId)
          if (refId) {
            cy.buySubscription(tariffId, user.token, cardId, refId).then(() => {
              var userInfo = {
                token: user.token,
                email: user.email,
              };
              return userInfo;
            });
            //если refId НЕ передан(Покупка подписки рефереру без refId)
          } else {
            cy.buySubscription(tariffId, user.token, cardId).then(() => {
              cy.wait(waitTime).then(() => {
                cy.log("Проверка активного статуса подписки пользователя");
                cy.checkStatusSubscription(user.token, 0).then(() => {
                  // cy.wait(70000).then(() => {
                  cy.log("Получение реф id");
                  cy.getReferrerId(user.token).then((refId) => {
                    cy.getExternalId(user.email).then((externalId) => {
                      var userInfo = {
                        refId: refId,
                        externalId: externalId,
                        token: user.token,
                        email: user.email,
                      };
                      //проверка refId и вывод ошибки если его нет
                      if (refId == null && refId.trim() == "") {
                        throw new Error("refId пустой");
                      } else {
                        return userInfo;
                      }
                    });
                    //});
                  });
                });
              });
            });
          }
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
    expect(response.body.data).to.have.property("refid").and.to.not.be.empty;
    return response.body.data.refid;
  });
});

//авторизация в CloudPayment
Cypress.Commands.add("loginCloudPayment", () => {
  cy.request({
    method: "POST",
    url: `https://merchant.cloudpayments.ru/api/auth/login`,

    body: {
      Status: { Key: "Active" },
      CultureName: { Key: "ru" },
      Email: "info@artforintrovert.ru",
      Password: "zCXx3q*ksKqMXb",
    },
  }).then((response) => {
    expect(response).to.have.property("status", 200);
    expect(response.headers).to.have.property("set-cookie");
    return response.headers["set-cookie"][0];
  });
});

//получение цены за подписку пользователя в CloudPayment
Cypress.Commands.add("getUserInCloudPayment", (userEmail, cookie) => {
  cy.request({
    method: "GET",
    url: `https://merchant.cloudpayments.ru/api/subscriptions?Payer=${userEmail}`,
    headers: {
      Cookie: cookie,
    },
  }).then((response) => {
    expect(response.status).to.eq(200);
    expect(response.body.Status).to.eq("Ok");
    //expect(response.body.Result[0].Status.Key).to.eq("Active");
    expect(response.body.Result[0]).to.have.property("Amount");
    return response.body.Result[0].Amount;
  });
});

//Проверка снижения цены за подписку у реферера
Cypress.Commands.add(
  "subscriptionPriceReductionCheck",
  (firstPrice, finalPrice, expectedPrice) => {
    if (finalPrice !== expectedPrice) {
      throw new Error(
        `Ожидаемая цена (${expectedPrice}) не соответствует полученной цене (${finalPrice}`
      );
    } else {
      cy.log(
        `Цена снизилась до ${finalPrice}. Первоначальная цена - ${firstPrice}`
      );
    }
  }
);

//покупка и отмена подписки
Cypress.Commands.add("buyAndCancelSubscription", () => {
  cy.registerAndGetUserData().then((user) => {
    cy.log(user.email);
    cy.getCard().then((card) => {
      cy.bindCardToUser(user.token, card.no3DSecureSuccess).then((cardId) => {
        cy.getTariff(monthTariff).then((monthTariff) => {
          cy.log("Покупка подписки");
          cy.buySubscription(monthTariff, user.token, cardId).then(() => {
            cy.wait(130000).then(() => {
              cy.checkStatusSubscription(user.token, 0).then(() => {
                cy.getReferrerId(user.token).then((refId)=>{
                cy.log("Отмена подписки");
                cy.request({
                  method: "POST",
                  url: `${apiNew}/subscriptions/cancel`,
                  headers: { authorization: `Bearer ${user.token}` },
                }).then((response) => {
                  expect(response).to.have.property("status", 200);
                  cy.wait(60000).then(() => {
                    cy.log("Проверка отмененного статуса подписки");
                    cy.checkStatusSubscription(user.token, 3).then(() => {
                      return {
                        email: user.email,
                        token: user.token,
                        refId: refId
                      };
                    });})
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
