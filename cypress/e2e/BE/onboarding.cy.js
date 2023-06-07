const apiNew = Cypress.env("apiNew");

const month = "month";
const year = "year";
const twoYears = "forever";

let user = {
  token:
    "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1IjoiNjQ1MjAwZTdhZGJlNjJjZmQxNmMyM2UyIiwicyI6IjE2ODMwOTU3ODMuODQ4MDE1IiwiZXhwaXJlc0F0IjoiMjAyMy0wNS0wM1QxMDozNjoyMy44NDgwMTc0OTkrMDM6MDAifQ.nwcPC1JL8igBgdQ5mY8bx5hSfFIMDIdakfHXEiYBqlY",
};

function onboarding(token, categoriesCount, goalsCount, courseCount) {
  cy.log("Получение одной рандомной категории и цели");
  //после токена передаем categories - count, после - goals - "all"/count
  cy.getCategoriesAndGoals(token, categoriesCount, goalsCount).then(
    (categories) => {
      cy.log(`Категория - ${categories.categoriesNames}`);
      cy.log(`Цель - ${categories.goalsNames}`);
      cy.log("Получение id курса по цели");
      //после токена передаем количество курсов, которые необходимо получить - count
      cy.getCourseFromGoal(token, categories.goalsIds, courseCount).then(
        (course) => {
          cy.log(`Курс - ${course.courseTitle}`);

          cy.log("Онбординг");
          cy.request({
            method: "POST",
            url: `${apiNew}/users/me/onboarding`,
            headers: {
              authorization: `Bearer ${token}`,
            },
            body: {
              categories: categories.categoryIds,
              courses: course.courseId,
              goals: categories.goalsIds,
            },
          }).then((response) => {
            expect(response).to.have.property("status", 204);

            cy.log(
              "Проверка формирования результатов онбординга у пользователя"
            );
            cy.checkOnboardingResults(
              token,
              categories.categoryIds,
              categories.goalsIds,
              course.courseId
            );
          });
        }
      );
    }
  );
}

it("Прохождение онбординга с 1 категорией, целью и курсом", () => {
  // cy.createUserWithSubscription(month).then((user) => {

  onboarding(user.token, 1, 1, 1);
  // })
});

it("Прохождение онбординга с 3 категорией, все цели и 5 курсов", () => {
  // cy.createUserWithSubscription(month).then((user) => {

  onboarding(user.token, 3, "all", 5);
  // });
});

it("Прохождение онбординга не выбрав ни одного курса", () => {
  // cy.createUserWithSubscription(month).then((user) => {

  onboarding(user.token, 1, "all", 0);
  // });
});
