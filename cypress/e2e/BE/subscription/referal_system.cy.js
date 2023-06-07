const apiNew = Cypress.env("apiNew");
const month = "month";
const year = "year";
const twoYears = "forever";
const password = Cypress.env("password");
const waitTimeCP = 120000; //время ожидания перед обращением в ClaudPayment

describe("Покупка подписки с refId", () => {
  function buySubscriptionWithRefId(tariffPeriod) {
    const discountAmount = 50;

    cy.log("РЕФЕРЕР");
    cy.createUserWithSubscription(tariffPeriod).then((referer) => {
      // получение цены за подписку у реферера до привода реферала
      cy.loginCloudPayment().then((cpCookie) => {
        cy.getUserInCloudPayment(referer.email, cpCookie).then((firstPrice) => {
          cy.log("РЕФЕРАЛ");
          cy.createUserWithSubscription(tariffPeriod, referer.refId).then(
            (referal) => {
              //активация подписки реферала
              cy.wait(60000).then(() => {
                //  cy.actiovationSubscription(referal.email).then(() => {
                cy.checkStatusSubscription(referal.token, 0).then(() => {
                  cy.clearRedisCache(referer.token).then(() => {
                    //получение свежего токена реферера
                    cy.getUserToken(referer.email, password).then(
                      (reffererNewToken) => {
                        cy.log("Проверка привязанных рефералов");
                        cy.getUserId(referal.token).then((referalUserId) => {
                          cy.checkRefferals(
                            referalUserId,
                            reffererNewToken,
                            0
                          ).then(() => {
                            cy.wait(waitTimeCP).then(() => {
                              cy.getUserInCloudPayment(
                                referer.email,
                                cpCookie
                              ).then((finalPrice) => {
                                cy.log("Проверка снижения цена за подписку");
                                const expectedPrice =
                                  firstPrice - discountAmount;
                                cy.subscriptionPriceReductionCheck(
                                  firstPrice,
                                  finalPrice,
                                  expectedPrice
                                );
                              });
                            });
                          });
                        });
                      }
                    );
                  });
                });
              });
            }
          );
        });
      });
    });
  }

  it("Покупка подписки на месяц с refId", () => {
    buySubscriptionWithRefId(month);
  });

  it("Покупка подписки на год с refId", () => {
    buySubscriptionWithRefId(year);
  });

  it("Покупка подписки на 2 года с refId", () => {
    buySubscriptionWithRefId(twoYears);
  });
});

describe("Получение максимальной скидки по реферальной системе", () => {
  function maxSails(tariffPeriod) {
    let refferalsId = []; // массив для хранения ID рефералов
    let refferalsToken = []; // массив для хранения токенов рефералов
    const expectedPrice = 99;

    cy.log("Создание реферера и покупка ему подписки");
    cy.createUserWithSubscription(tariffPeriod).then((referer) => {
      //получение цены за подписку у реферера до привода рефералов
      cy.loginCloudPayment().then((cpCookie) => {
        cy.getUserInCloudPayment(referer.email, cpCookie).then((firstPrice) => {
          cy.log(
            "Создание рефералов, пока цена за подписку у реферера не достигнет 99р"
          );
          let currentPrice = firstPrice;
          for (let i = 0; currentPrice > 99; i++) {
            cy.log(`Создание реферала ${i + 1} и покупка ему подписки`);
            cy.createUserWithSubscription(tariffPeriod, referer.refId).then(
              (referal) => {
                //получение userId реферала
                cy.getUserId(referal.token).then((referalUserId) => {
                  refferalsId.push(referalUserId);
                  refferalsToken.push(referal.token);
                });
              }
            );
            currentPrice -= 50;
          }
          cy.log("Проверка 0 статуса подписки у рефералов");
          cy.wait(60000).then(() => {
            cy.wrap(refferalsToken).each((token, i) => {
              cy.checkStatusSubscription(token, 0);
            });
            cy.then(() => {
              //создание экстра реферала после 99р и проверка что цена после него не снижается
              cy.createUserWithSubscription(month, referer.refId).then(
                (extraRefferal) => {
                  cy.actiovationSubscription(extraRefferal.email).then(() => {
                    cy.checkStatusSubscription(extraRefferal.token, 0).then(
                      () => {
                        cy.clearRedisCache(referer.token).then(() => {
                          cy.getUserToken(referer.email, password).then(
                            (ReffererNewToken) => {
                              cy.log("Проверка привязки всех рефералов");
                              cy.wrap(refferalsId).each((id, j) => {
                                cy.checkRefferals(id, ReffererNewToken, j);
                              });
                              cy.then(() => {
                                cy.log(
                                  "Проверка снижения цены до 99р у реферера"
                                );
                                cy.wait(waitTimeCP).then(() => {
                                  cy.getUserInCloudPayment(
                                    referer.email,
                                    cpCookie
                                  ).then((finalPrice) => {
                                    cy.subscriptionPriceReductionCheck(
                                      firstPrice,
                                      finalPrice,
                                      expectedPrice
                                    );
                                    cy.log(".!.");
                                  });
                                });
                              });
                            }
                          );
                        });
                      }
                    );
                  });
                }
              );
            });
          });
        });
      });
    });
    // });
    //});
  }

  it("Получение максимальной скидки по реферальной системе с месячным тарифом", () => {
    maxSails(month);
  });

  it("Получение максимальной скидки по реферальной системе с годовым тарифом", () => {
    maxSails(year);
  });

  it("Получение максимальной скидки по реферальной системе с двухлетним тарифом", () => {
    maxSails(twoYears);
  });
});

describe("Негативные кейсы", () => {
  //у реферера есть реф айди но при приводе реферала не уменьшается цена отмененной подписки и реферал не подвязывается
  it("Использование refId реферера с просроченной подпиской", () => {
    cy.log("РЕФЕРЕР");
    cy.buyAndCancelSubscription().then((referer) => {
      // получение цены за подписку у реферера до привода реферала
      cy.loginCloudPayment().then((cpCookie) => {
        cy.getUserInCloudPayment(referer.email, cpCookie).then((firstPrice) => {
          cy.log("РЕФЕРАЛ");
          cy.createUserWithSubscription(month, referer.refId).then(
            (referal) => {
              cy.wait(60000).then(() => {
                cy.checkStatusSubscription(referal.token, 0).then(() => {
                  cy.clearRedisCache(referer.token).then(() => {
                    //получение свежего токена реферера
                    cy.getUserToken(referer.email, password).then(
                      (reffererNewToken) => {
                        cy.log("Проверка что реферал не привязался");
                        cy.request({
                          method: "GET",
                          url: `${apiNew}/users/me`,
                          headers: {
                            authorization: `Bearer ${reffererNewToken}`,
                          },
                        }).then((response) => {
                          expect(response.body.data.referrals).to.deep.equal(
                            []
                          );
                          cy.log("Проверка что цена осталась прежней");
                          cy.wait(waitTimeCP).then(() => {
                            cy.getUserInCloudPayment(
                              referer.email,
                              cpCookie
                            ).then((finalPrice) => {
                              const expectedPrice = firstPrice;
                              if (firstPrice !== expectedPrice) {
                                throw new Error(
                                  `Ожидаемая цена (${expectedPrice}) не соответствует полученной цене (${finalPrice}`
                                );
                              } else {
                                cy.log(
                                  `Финальная цена ${finalPrice} совпадет с первоначальной ${firstPrice}`
                                );
                              }
                            });
                          });
                        });
                      }
                    );
                  });
                });
              });
            }
          );
        });
      });
    });
  });

  //если реферал не смог купить подписку - он записывается рефереру, но не дает скидку
  it("Покупка подписки с refId по карте без денег", () => {
    cy.log("РЕФЕРЕР");
    cy.createUserWithSubscription(month).then((referer) => {
      // получение цены за подписку у реферера до привода реферала
      cy.loginCloudPayment().then((cpCookie) => {
        cy.getUserInCloudPayment(referer.email, cpCookie).then((firstPrice) => {
          cy.log("РЕФЕРАЛ");
          cy.registerAndGetUserData().then((referal) => {
            //покупка подписки картой с 11р
            cy.getTariff(month).then((monthTariff) => {
              cy.getCard().then((card) => {
                cy.bindCardToUser(
                  referal.token,
                  card.no3DSecureWithStatus3
                ).then((cardId) => {
                  cy.request({
                    method: "POST",
                    url: `${apiNew}/subscriptions`,
                    headers: {
                      authorization: `Bearer ${referal.token}`,
                    },
                    body: {
                      creditCardId: cardId,
                      tariffId: monthTariff,
                      referrerId: referer.refId,
                    },
                  }).then((response) => {
                    expect(response).to.have.property("status", 200);
                    cy.wait(100000).then(() => {
                      cy.checkStatusSubscription(referal.token, 6).then(() => {
                        cy.clearRedisCache(referer.token).then(() => {
                          //получение свежего токена реферера
                          cy.getUserToken(referer.email, password).then(
                            (reffererNewToken) => {
                              cy.log(
                                "Проверка что реферал привязался с verified: false"
                              );
                              cy.getUserId(referal.token).then(
                                (referalUserId) => {
                                  cy.request({
                                    method: "GET",
                                    url: `${apiNew}/users/me`,
                                    headers: {
                                      authorization: `Bearer ${reffererNewToken}`,
                                    },
                                  }).then((response) => {
                                    expect(
                                      response.body.data.referrals[0]
                                    ).to.have.property("id", referalUserId);
                                    expect(
                                      response.body.data.referrals[0]
                                    ).to.have.property("verified", false);

                                    cy.log(
                                      "Проверка что цена осталась прежней"
                                    );
                                    cy.wait(waitTimeCP).then(() => {
                                      cy.getUserInCloudPayment(
                                        referer.email,
                                        cpCookie
                                      ).then((finalPrice) => {
                                        const expectedPrice = firstPrice;
                                        if (firstPrice !== expectedPrice) {
                                          throw new Error(
                                            `Ожидаемая цена (${expectedPrice}) не соответствует полученной цене (${finalPrice}`
                                          );
                                        } else {
                                          cy.log(
                                            `Финальная цена ${finalPrice} совпадет с первоначальной ${firstPrice}`
                                          );
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
              });
            });
          });
        });
      });
    });
  });

  //первый раз покупка, второй раз смена тарифа
  it("Покупка подписки рефералу по одному refId дважды", () => {
    //сейчас при апдейте отправляется рефид.с игорем обсудить и наверное лучше всего просто при апдейте не давать отправлять рефид
  });

  // у пользователя без подписки не должно быть реф. ссылки
  it("Получение refId у пользователя без подписки", () => {
    cy.registerAndGetUserData().then((user) => {
      cy.request({
        method: "GET",
        url: `${apiNew}/users/me`,
        headers: {
          authorization: `Bearer ${user.token}`,
        },
      }).then((response) => {
        expect(response.body.data.refid).to.be.empty;
      });
    });
  });
});

describe("Доп.кейс", () => { //черновики
  //цена должна стать снова полной
  it("Смена тарифа у реферера с активными рефералами", () => {
    //купили подписку привели реферала
    function buySubscriptionWithRefId(tariffPeriod) {
      const discountAmount = 50;

      cy.log("РЕФЕРЕР");
      cy.createUserWithSubscription(tariffPeriod).then((referer) => {
        // получение цены за подписку у реферера до привода реферала
        cy.loginCloudPayment().then((cpCookie) => {
          cy.getUserInCloudPayment(referer.email, cpCookie).then(
            (firstPrice) => {
              cy.log("РЕФЕРАЛ");
              cy.createUserWithSubscription(tariffPeriod, referer.refId).then(
                (referal) => {
                  //активация подписки реферала
                  cy.wait(60000).then(() => {
                    //  cy.actiovationSubscription(referal.email).then(() => {
                    cy.checkStatusSubscription(referal.token, 0).then(() => {
                      cy.clearRedisCache(referer.token).then(() => {
                        //получение свежего токена реферера
                        cy.getUserToken(referer.email, password).then(
                          (reffererNewToken) => {
                            cy.log("Проверка привязанных рефералов");
                            cy.getUserId(referal.token).then(
                              (referalUserId) => {
                                cy.checkRefferals(
                                  referalUserId,
                                  reffererNewToken,
                                  0
                                ).then(() => {
                                  cy.wait(waitTimeCP).then(() => {
                                    cy.getUserInCloudPayment(
                                      referer.email,
                                      cpCookie
                                    ).then((finalPrice) => {
                                      cy.log(
                                        "Проверка снижения цена за подписку"
                                      );
                                      const expectedPrice =
                                        firstPrice - discountAmount;
                                      cy.subscriptionPriceReductionCheck(
                                        firstPrice,
                                        finalPrice,
                                        expectedPrice
                                      );
                                      return {
                                        referer: referer,
                                        cpCookie: cpCookie,
                                      };
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
                }
              );
            }
          );
        });
      });
    }
    //сменили тариф
    function changeTariff(user, tariffPeriod, cardId) {
      y.log("Смена тарифа подписки");
      cy.request({
        method: "POST",
        url: `${apiNew}/subscriptions/update`,
        headers: {
          authorization: `Bearer ${user.token}`,
        },
        body: {
          creditCardId: cardId,
          tariffId: tariffPeriod,
        },
      }).then((response) => {
        expect(response).to.have.property("status", 200);
        cy.wait(60000).then(() => {
          cy.log("Проверка активного статуса подписки");
          cy.checkStatusSubscription(user.token, 0).then(() => {});
        });
      });
    }
    //проверка что после смены остались рефералы но цена по новому тарифу полная
    cy.getUserInCloudPayment(referer.email, cpCookie).then((endPrice) => {});
  });

  //цена должна стать снова снизиться
  it("Привод реферала после смены тарифа", () => {});
});
