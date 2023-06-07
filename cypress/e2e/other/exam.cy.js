const id = "60e2fbafa8b399881bb63a31";

it("получение ответов на экзамен", () => {
  cy.request({
    method: "POST",
    url: "https://new.artforintrovert.ru:3000/users-admin-panel/sign-in",
    body: {
      username: "natali",
      passHash:
        "9e3f36731c954ef4a97320620aa6e5d98e6984e35b6c72c9bc66d7a5520b530e",
    },
  }).then((response) => {
    expect(response).to.have.property("status", 200);
    expect(response.body.user).to.have.property("token");
    var accessToken = response.body.user["token"];

    cy.request({
      method: "GET",
      url: "https://new.artforintrovert.ru:3000/products/" + id,
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
            examInfo.push({
              question: question.question.text.all,
              answer: answer.answer.all,
            });
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
            .map((info) => `${info.question}: ${info.answer}`)
            .join("\n") + "\n"
        );
      }
    });
  });
});
