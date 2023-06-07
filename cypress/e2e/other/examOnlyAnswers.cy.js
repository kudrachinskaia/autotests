const id = "639c000fd00e3b00282ad470";

it("получение ответов на экзамен", () => {
  cy.request({
    method: "POST",
    url: "https://dev.artforintrovert.ru:3000/users-admin-panel/sign-in",
    body: {
      username: "natali",
      passHash:
        "6413a09572b75a73cecda29713f72756f7ed56191e132954f47748c07ffffc85",
    },
  }).then((response) => {
    expect(response).to.have.property("status", 200);
    expect(response.body.user).to.have.property("token");
    var accessToken = response.body.user["token"];

    cy.request({
      method: "GET",
      url: "https://dev.artforintrovert.ru:3000/products/" + id,
      headers: {
        authorization: accessToken,
      },
    }).then((response) => {
      expect(response).to.have.property("status", 200);
      expect(
        response.body.product.videocourseOnly.assignedExam
      ).to.have.property("questions");
      const nameExam =
        response.body.product.videocourseOnly.assignedExam.title.all;
      var questions =
        response.body.product.videocourseOnly.assignedExam.questions;
      const examInfo = [];
      for (const question of questions) {
        for (const answer of question.answers) {
          if (answer.isRight) {
            examInfo.push(answer.answer.all);
          }
        }
      }
      if (examInfo.length === 0) {
        cy.log("Похоже, что у курса нет экзамена");
      } else {
        const title = nameExam;
        const filename = `/Users/natalie/workspace/introverts/examAnswers/${title}.json`;

        cy.writeFile(
          filename,
          examInfo
            .map((info) => `${info}`)
            .join("\n") + "\n"
        );
      }
    });
  });
});
