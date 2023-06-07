it("Удаление пользователей @forautotest.ru из бд", () => {
  cy.deleteMany(
    { email: { $regex: /@forautotest\.ru$/ } },
    { collection: "users", database: "afi-dev" }
  ).then((result) => {
    cy.log(result);
  });
});
