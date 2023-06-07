it("Регистрация пользователя", () => {
    cy.registerAndGetUserData().then((user) => {
      cy.log("почта",user.email);
      cy.log("access token", user.token);
    });
  });
