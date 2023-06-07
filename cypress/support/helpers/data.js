const apiNew = Cypress.env("apiNew");

//Получение нужной карты по критериям 3D secure и успешности
Cypress.Commands.add("getCard", () => {
  const cards = {
    no3DSecureSuccess: [4000000000003055, 5205000000003055, 2202000000003055],
    no3DSecureUnsuccess: 4000056655665556,
    no3DSecureWithStatus3: 4111111111111111,
    with3DSecureSuccess: 4242424242424242,
    with3DSecureUnsuccess: 4012888888881881,
  };
  var randomIndex = Math.floor(Math.random() * cards.no3DSecureSuccess.length);
  var cardData = {
    no3DSecureSuccess: cards.no3DSecureSuccess[randomIndex],
    no3DSecureUnsuccess: cards.no3DSecureUnsuccess,
    no3DSecureWithStatus3: cards.no3DSecureWithStatus3,
    with3DSecureSuccess: cards.with3DSecureSuccess,
    with3DSecureUnsuccess: cards.with3DSecureUnsuccess,
  };
  return cardData;
});

//Получение первых 6 и последних 4 цифр карты
Cypress.Commands.add("getFirstAndLastNumberCard", (cartNumber) => {
  var firstSix = Math.floor(cartNumber / 1e10);
  var lastFour = cartNumber % 1e4;
  var data = {
    first: `${firstSix}`,
    last: `${lastFour}`,
  };
  return data;
});

//Получение криптограммы карты
Cypress.Commands.add("getCriptogramm", (cartNumber) => {
  //no3DSecureSuccess
  if (cartNumber == 4000000000003055) {
    return "eyJUeXBlIjoiQ2xvdWRDYXJkIiwiQnJvd3NlckluZm9CYXNlNjQiOiJleUpCWTJObGNIUklaV0ZrWlhJaU9pSXFMeW9pTENKS1lYWmhSVzVoWW14bFpDSTZabUZzYzJVc0lrcGhkbUZUWTNKcGNIUkZibUZpYkdWa0lqcDBjblZsTENKTVlXNW5kV0ZuWlNJNkluSjFJaXdpUTI5c2IzSkVaWEIwYUNJNklqSTBJaXdpU0dWcFoyaDBJam9pT1RBd0lpd2lWMmxrZEdnaU9pSXhORFF3SWl3aVZHbHRaVnB2Ym1VaU9pSXRORGd3SWl3aVZYTmxja0ZuWlc1MElqb2lUVzk2YVd4c1lTODFMakFnS0UxaFkybHVkRzl6YURzZ1NXNTBaV3dnVFdGaklFOVRJRmdnTVRCZk1UVmZOeWtnUVhCd2JHVlhaV0pMYVhRdk5qQTFMakV1TVRVZ0tFdElWRTFNTENCc2FXdGxJRWRsWTJ0dktTQldaWEp6YVc5dUx6RTJMakVnVTJGbVlYSnBMell3TlM0eExqRTFJbjA9IiwiRm9ybWF0IjoxLCJDYXJkSW5mbyI6eyJGaXJzdFNpeERpZ2l0cyI6IjQwMDAwMCIsIkxhc3RGb3VyRGlnaXRzIjoiMzA1NSIsIkV4cERhdGVZZWFyIjoiMjQiLCJFeHBEYXRlTW9udGgiOiIxMiJ9LCJLZXlWZXJzaW9uIjoiMiIsIlZhbHVlIjoiYTJHVXBSSWtkU2NmaExlT3NTVnFVNVpNSkFoYVBZejNiVjNoUmhrRTBjUTVaNnlweTBDRUVBMFE1dzFKanZBZmdkK201Mk1aZURzY1JOM2xEejZGYnh6a2hrUEs2OWNydlc1dWpDcm5nQTNrSlZnRW5FaWQwTkJPNjhmNVdIV0dmbXc1VUpwUysxNjZpd0VnZWVRdTdiRWR1Vlk0UnpMUHdaOXpvUzl6MnRLeHQzc1d3aW1EWlVOeHREMEpDdHdmcW03RUkxd2Y3NXg1ZEFvT2I0S0lqc0t6UWVoazJtQTc2dDUyeHRIMUhJT0Z1bXUyaEt2RzFpeUg0N01rdUZRMFFkdFVsRUI1cmhsU08yNnhBWXd2MnAxTmJpSTllckM1cHAzM2x5YnYwb0NINW9wVUNMZkRocHJpYmdxOHAyWUwzTDR2QXkvMTh2NW9zZmNhanJCV2VBPT0ifQ==";
  } else if (cartNumber == 5205000000003055) {
    return "eyJUeXBlIjoiQ2xvdWRDYXJkIiwiQnJvd3NlckluZm9CYXNlNjQiOiJleUpCWTJObGNIUklaV0ZrWlhJaU9pSXFMeW9pTENKS1lYWmhSVzVoWW14bFpDSTZabUZzYzJVc0lrcGhkbUZUWTNKcGNIUkZibUZpYkdWa0lqcDBjblZsTENKTVlXNW5kV0ZuWlNJNkluSjFJaXdpUTI5c2IzSkVaWEIwYUNJNklqSTBJaXdpU0dWcFoyaDBJam9pT1RBd0lpd2lWMmxrZEdnaU9pSXhORFF3SWl3aVZHbHRaVnB2Ym1VaU9pSXRORGd3SWl3aVZYTmxja0ZuWlc1MElqb2lUVzk2YVd4c1lTODFMakFnS0UxaFkybHVkRzl6YURzZ1NXNTBaV3dnVFdGaklFOVRJRmdnTVRCZk1UVmZOeWtnUVhCd2JHVlhaV0pMYVhRdk5qQTFMakV1TVRVZ0tFdElWRTFNTENCc2FXdGxJRWRsWTJ0dktTQldaWEp6YVc5dUx6RTJMakVnVTJGbVlYSnBMell3TlM0eExqRTFJbjA9IiwiRm9ybWF0IjoxLCJDYXJkSW5mbyI6eyJGaXJzdFNpeERpZ2l0cyI6IjUyMDUwMCIsIkxhc3RGb3VyRGlnaXRzIjoiMzA1NSIsIkV4cERhdGVZZWFyIjoiMjQiLCJFeHBEYXRlTW9udGgiOiIxMiJ9LCJLZXlWZXJzaW9uIjoiMiIsIlZhbHVlIjoiRU44aHAzZVRlanR3RVNRQlBEQWZnV2VyVFpudlM2aXRzME1pR1d3d3ZPTTRBdTFtQ05ON0FOR01Pb3VOdklOaWIyZHY0YnZ5UUUzN3lGWFdQemtYVkk1SGNpQitDV2pKOFlsdWVHV1pFQ3lsTHpSRGtVUWNubVBnK3FNVUQzbERBOE1wUnpvanNCQm8vSzUyZy9rTEp0aTFidXllRkc4dTNWaXJQK2pxK2xib0RRWWNDNmh1Y0g3NmtsNVUrNkJsNGRhY1YvTmorYk11WnJQaDBSYUM5Z29KdHZyaGlIOHkxREV2RlVXNG5BcUhqeEZtSnZlZitFNHNYN3lzVlZHcWZ5YytIZmxXTHQ5Sld0OHdhaFQya0IrM0I2blZpckFnZmthQzJES2hwOThwYVp5d3FUSm9yT09vMGp5SkN2NnRrNUUwVEl5WWNObkdsOUMvRk9OdnFnPT0ifQ==";
  } else if (cartNumber == 2202000000003055) {
    return "eyJUeXBlIjoiQ2xvdWRDYXJkIiwiQnJvd3NlckluZm9CYXNlNjQiOiJleUpCWTJObGNIUklaV0ZrWlhJaU9pSXFMeW9pTENKS1lYWmhSVzVoWW14bFpDSTZabUZzYzJVc0lrcGhkbUZUWTNKcGNIUkZibUZpYkdWa0lqcDBjblZsTENKTVlXNW5kV0ZuWlNJNkluSjFJaXdpUTI5c2IzSkVaWEIwYUNJNklqSTBJaXdpU0dWcFoyaDBJam9pT1RBd0lpd2lWMmxrZEdnaU9pSXhORFF3SWl3aVZHbHRaVnB2Ym1VaU9pSXRORGd3SWl3aVZYTmxja0ZuWlc1MElqb2lUVzk2YVd4c1lTODFMakFnS0UxaFkybHVkRzl6YURzZ1NXNTBaV3dnVFdGaklFOVRJRmdnTVRCZk1UVmZOeWtnUVhCd2JHVlhaV0pMYVhRdk5qQTFMakV1TVRVZ0tFdElWRTFNTENCc2FXdGxJRWRsWTJ0dktTQldaWEp6YVc5dUx6RTJMakVnVTJGbVlYSnBMell3TlM0eExqRTFJbjA9IiwiRm9ybWF0IjoxLCJDYXJkSW5mbyI6eyJGaXJzdFNpeERpZ2l0cyI6IjIyMDIwMCIsIkxhc3RGb3VyRGlnaXRzIjoiMzA1NSIsIkV4cERhdGVZZWFyIjoiMjQiLCJFeHBEYXRlTW9udGgiOiIxMiJ9LCJLZXlWZXJzaW9uIjoiMiIsIlZhbHVlIjoiUDh4Rk1KK2ZlL2NYYlZSaDJYZmw0dkY5TjlCa1Z5bFlzUGtlSXg2SVhiZVpBaFcrNzFjaWc2QU8vKzNjUnNFdGxoMmFUTTBzc21idGxYWUpHUGk1NWlsZE1oSlZsVWpTcWlyUXlZV1MrVDZQcmIrZmpIblA3TFZBUi9DTUpYV1hSVldKZFlCQ1VHSEVORGdRR1FuWFh6ZlhYaXp2RVFxUmVnaGU4S0JRQ2RWNTJjb0NhTVA3dzg1LzhFa0Z3ZGEyTUR2a3c2dEgzaFpCeEVxYVBIeWlKaGdZUUcvZ2tzWnVLTEthekx0OVVhd2Y2ekYycy9OWkpKK3RJK2k0Z01JSkczRU5YNXc4WHQ5OWRlakRxYldNd2VUNjZWYy8xRVkwWHpSTmFDNmJRVTFFMVNKcXZkWDZuT1JCMU9VUU1PSE9XSlIrZVMyZ25ZZDR0d3dvbU43Tk5BPT0ifQ==";
  }
  //no3DSecureUnsuccess
  else if (cartNumber == 4000056655665556) {
    return "eyJUeXBlIjoiQ2xvdWRDYXJkIiwiQnJvd3NlckluZm9CYXNlNjQiOiJleUpCWTJObGNIUklaV0ZrWlhJaU9pSXFMeW9pTENKS1lYWmhSVzVoWW14bFpDSTZabUZzYzJVc0lrcGhkbUZUWTNKcGNIUkZibUZpYkdWa0lqcDBjblZsTENKTVlXNW5kV0ZuWlNJNkluSjFJaXdpUTI5c2IzSkVaWEIwYUNJNklqSTBJaXdpU0dWcFoyaDBJam9pT1RBd0lpd2lWMmxrZEdnaU9pSXhORFF3SWl3aVZHbHRaVnB2Ym1VaU9pSXRORGd3SWl3aVZYTmxja0ZuWlc1MElqb2lUVzk2YVd4c1lTODFMakFnS0UxaFkybHVkRzl6YURzZ1NXNTBaV3dnVFdGaklFOVRJRmdnTVRCZk1UVmZOeWtnUVhCd2JHVlhaV0pMYVhRdk5qQTFMakV1TVRVZ0tFdElWRTFNTENCc2FXdGxJRWRsWTJ0dktTQldaWEp6YVc5dUx6RTJMakVnVTJGbVlYSnBMell3TlM0eExqRTFJbjA9IiwiRm9ybWF0IjoxLCJDYXJkSW5mbyI6eyJGaXJzdFNpeERpZ2l0cyI6IjQwMDAwNSIsIkxhc3RGb3VyRGlnaXRzIjoiNTU1NiIsIkV4cERhdGVZZWFyIjoiMjQiLCJFeHBEYXRlTW9udGgiOiIxMiJ9LCJLZXlWZXJzaW9uIjoiMiIsIlZhbHVlIjoiRFM0Z3NobnA1M0RJckZUMDVYVWJUcDQ1OVZDbitlWVl3dm5NcDVWSFY4Qmd4dGc3TjdsWWxzL0k0T21FandCU2N4ejJLeHRhYlZEbGN5S2VGeFY3OWplQlNFUFpndG9HRXFyL09wM2VnUGRockFZTkExZk9tQnhBb1ZvTE5ndDFKZ1NoaHJuRExmREp4b3JJaGNBQStvUStDdURlY1lJQnE0bHZNZUxDZ1loNVF0Um5XWExrY2tHSHRDUEdycm1VMXU1WW9acFU3dmJXZElQYjFMUmFuOTd0Q1dqam84Qy83Wmt1YnVkQm01TjhtN09hb3ZqY2ZibS95YVRnM3JEbDlweTVnUE5FZHFvK0hhRkhlL3VrRndTdE1idXdLOUNoeW5XRnNITDhEQjY5NkxBcUNzNVpEdk9iWHRyR3huL0c3d0ZqREprczZwdlFDczk3SmdLZlNRPT0ifQ==";
  }
  //no3DSecureWithStatus3
  else if (cartNumber == 4111111111111111) {
    return "eyJUeXBlIjoiQ2xvdWRDYXJkIiwiQnJvd3NlckluZm9CYXNlNjQiOiJleUpCWTJObGNIUklaV0ZrWlhJaU9pSXFMeW9pTENKS1lYWmhSVzVoWW14bFpDSTZabUZzYzJVc0lrcGhkbUZUWTNKcGNIUkZibUZpYkdWa0lqcDBjblZsTENKTVlXNW5kV0ZuWlNJNkluSjFJaXdpUTI5c2IzSkVaWEIwYUNJNklqSTBJaXdpU0dWcFoyaDBJam9pT1RBd0lpd2lWMmxrZEdnaU9pSXhORFF3SWl3aVZHbHRaVnB2Ym1VaU9pSXRORGd3SWl3aVZYTmxja0ZuWlc1MElqb2lUVzk2YVd4c1lTODFMakFnS0UxaFkybHVkRzl6YURzZ1NXNTBaV3dnVFdGaklFOVRJRmdnTVRCZk1UVmZOeWtnUVhCd2JHVlhaV0pMYVhRdk5qQTFMakV1TVRVZ0tFdElWRTFNTENCc2FXdGxJRWRsWTJ0dktTQldaWEp6YVc5dUx6RTJMakVnVTJGbVlYSnBMell3TlM0eExqRTFJbjA9IiwiRm9ybWF0IjoxLCJDYXJkSW5mbyI6eyJGaXJzdFNpeERpZ2l0cyI6IjQxMTExMSIsIkxhc3RGb3VyRGlnaXRzIjoiMTExMSIsIkV4cERhdGVZZWFyIjoiMjQiLCJFeHBEYXRlTW9udGgiOiIxMiJ9LCJLZXlWZXJzaW9uIjoiMiIsIlZhbHVlIjoiY01GTXp6QUdkQlY3Q1NHWHR1QWE2MXhvRFZmcUxtUExsaTEwREZZK2xXbWprOVozaU1DUDdiQzNSaWJlTGZUWEd1WVRqQlpDTVFndGY4VkhsY1M4am1WdlcxNW5wZ3Qrd2xXSUhUbGRhRTJ4RUNhb3FuckdxQVFUdndPR0MyMmg2T0FVOHhMUnljMWtZYmV1THg5Sk9yZUJzVEhSbWtmRG02ZmR3bjlmZW8wOEpFWUh4UklOYlpFd01VKzFtVDBNcUhacEJFa1NOaXlZQWVMRDBJRUNUMlZhdFpOTXF4U0RsSUwvNnNGRkQ4WTdxeDc2bHk0Y2JDZ2loTWlEbVk5Z1JiYngxdnRIakRFTlp4RXIvb2drdkp5LzN6Q0tIRDBEZmx1VHhtWDcxcEMrTnFXZHdwWlZSUTB3MTNlRit3amExeEl6L0x0U29rSHVCa0N3ZGwxajdBPT0ifQ==";
  }
  //with3DSecureUnsuccess
  else if (cartNumber == 4012888888881881) {
    return "eyJUeXBlIjoiQ2xvdWRDYXJkIiwiQnJvd3NlckluZm9CYXNlNjQiOiJleUpCWTJObGNIUklaV0ZrWlhJaU9pSXFMeW9pTENKS1lYWmhSVzVoWW14bFpDSTZabUZzYzJVc0lrcGhkbUZUWTNKcGNIUkZibUZpYkdWa0lqcDBjblZsTENKTVlXNW5kV0ZuWlNJNkluSjFJaXdpUTI5c2IzSkVaWEIwYUNJNklqSTBJaXdpU0dWcFoyaDBJam9pT1RBd0lpd2lWMmxrZEdnaU9pSXhORFF3SWl3aVZHbHRaVnB2Ym1VaU9pSXRORGd3SWl3aVZYTmxja0ZuWlc1MElqb2lUVzk2YVd4c1lTODFMakFnS0UxaFkybHVkRzl6YURzZ1NXNTBaV3dnVFdGaklFOVRJRmdnTVRCZk1UVmZOeWtnUVhCd2JHVlhaV0pMYVhRdk5qQTFMakV1TVRVZ0tFdElWRTFNTENCc2FXdGxJRWRsWTJ0dktTQldaWEp6YVc5dUx6RTJMakVnVTJGbVlYSnBMell3TlM0eExqRTFJbjA9IiwiRm9ybWF0IjoxLCJDYXJkSW5mbyI6eyJGaXJzdFNpeERpZ2l0cyI6IjQwMTI4OCIsIkxhc3RGb3VyRGlnaXRzIjoiMTg4MSIsIkV4cERhdGVZZWFyIjoiMjQiLCJFeHBEYXRlTW9udGgiOiIxMiJ9LCJLZXlWZXJzaW9uIjoiMiIsIlZhbHVlIjoiZ2FJU1JKWVRYY0ZaSk43OXFVTWc5dTRoYU9Oa2dTVTczcGdyNVVnRFBNa1ltVjRLU0hMT0hXT1d3V1A1RTlmWjdCTDE1S3RzTHpXTGZqd3BGajR2cmRrd2Y2T3d5c1p0WUhyZ0l1TDFDdmRvZTJYek5TbE1ERW5BY290NzNicjE1bTBpcTlHR3VNQ0c0RE5SQktjLzc1aCtUcnI2dFhDamx1S0hYZHFpSEFVSTZUcXBlQkZ1bUNqQVlTMkxhZy9BeStQNkFXU1hyZm9yR21rOVNmbWhoa1piVkJTV0Zhd3d3Q3ZjRWp0amJXNkk0RS9uL2l5TERTWjFNNGQxL05jSllkUzRFandBWS81aUhWbHJycnJyTTZITmY5amZQbEdsTGJHdDVUa1ZDN1JZc3JPK2hVSlFiaElaQy9ScFpXRm1lL1FjM2xuaXdnMCtWS204OW5nbkpBPT0ifQ==";
  }
  //with3DSecureSuccess
  else {
    return "eyJUeXBlIjoiQ2xvdWRDYXJkIiwiQnJvd3NlckluZm9CYXNlNjQiOiJleUpCWTJObGNIUklaV0ZrWlhJaU9pSXFMeW9pTENKS1lYWmhSVzVoWW14bFpDSTZabUZzYzJVc0lrcGhkbUZUWTNKcGNIUkZibUZpYkdWa0lqcDBjblZsTENKTVlXNW5kV0ZuWlNJNkluSjFJaXdpUTI5c2IzSkVaWEIwYUNJNklqSTBJaXdpU0dWcFoyaDBJam9pT1RBd0lpd2lWMmxrZEdnaU9pSXhORFF3SWl3aVZHbHRaVnB2Ym1VaU9pSXRORGd3SWl3aVZYTmxja0ZuWlc1MElqb2lUVzk2YVd4c1lTODFMakFnS0UxaFkybHVkRzl6YURzZ1NXNTBaV3dnVFdGaklFOVRJRmdnTVRCZk1UVmZOeWtnUVhCd2JHVlhaV0pMYVhRdk5qQTFMakV1TVRVZ0tFdElWRTFNTENCc2FXdGxJRWRsWTJ0dktTQldaWEp6YVc5dUx6RTJMakVnVTJGbVlYSnBMell3TlM0eExqRTFJbjA9IiwiRm9ybWF0IjoxLCJDYXJkSW5mbyI6eyJGaXJzdFNpeERpZ2l0cyI6IjQyNDI0MiIsIkxhc3RGb3VyRGlnaXRzIjoiNDI0MiIsIkV4cERhdGVZZWFyIjoiMjQiLCJFeHBEYXRlTW9udGgiOiIxMiJ9LCJLZXlWZXJzaW9uIjoiMiIsIlZhbHVlIjoiRGxGcnJUQmdTTjNGSU5yOGkrbER6dzhlV3hoNWUxUU9OcTdKd2JLbXZoa2ROS3dnbi9PdEF4ZUluQlloSCtxWGl0blBqdUFtdldCQnZzcDdHVlhyam1vSVlLcTBmYVl3b3BDeVJmVjZuZGdQQjU5bGljZ0hwOG96V05ieEQyM3MyRzducnpZQmlteFBHQmU3eTJqcnFQY3dqWG5wL21jRnZJM1dxVjI5YjM5OENlVXF3NHl4MFR3NjhSQUFZeUs3cTVhdnlseTZjVzJZbFE1WjNtbWRQRE0wcFpGT1hCaU9YNEtUZFFzT2FKZ2tHWVAySXQyVHAySjBZVlNKb1Y0WjR1ZVUyUEdnUUJSYWxxbXZRNTRWckREdzFySEpSNDU2THQ2RTBOSW55SlhmNzA2NVRNSDJlWU0yV3ZML2ZRbEpQeHorcldnQzhWRGdORnhnL0lWbFpRPT0ifQ==";
  }
});

//добавить вывод цены для тестов по реф системе
/*Получение id необходимого тарифа
forever
year
month
*/
Cypress.Commands.add("getTariff", (periodLabel) => {
  return cy
    .request({
      method: "GET",
      url: `${apiNew}/tariffs`,
    })
    .then((result) => {
      const tariff = result.body.data.find(
        (t) => t.periodLabel === periodLabel
      );
      if (!tariff) {
        throw new Error(`Нет тарифа с таким периодом'${periodLabel}'`);
      }
      return tariff.id;
    });
});

//Генерация cardExpireDate
Cypress.Commands.add("getCardExpireDate", () => {
  var month = Math.floor(Math.random() * 11) + 1;
  if (month < 10) {
    month = `0${month}`;
  }
  var year = Math.floor(Math.random() * 10) + 23;
  var date = `${month}/${year}`;
  return date;
});

//Возвращает externalId
Cypress.Commands.add("getExternalId", (email) => {
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
      if (!subscription) {
        throw new Error(`У пользователя ${userId} подписка не найдена`);
      }
      if (subscription.status !== 0) {
        throw new Error(`У пользователя ${userId} подписка не активна`);
      }
      if (!subscription.externalId) {
        throw new Error(
          `External ID не найден для подписки ${subscription._id}`
        );
      }
      return subscription.externalId;
    });
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

/*получение id категорий курсов(categoryId) и id подкатегорий(цели)(goalId)
количество категорий отдается в зависимости от переданного параметра categoryCount, количество целей от goalsCount*/
Cypress.Commands.add(
  "getCategoriesAndGoals",
  (token, categoryCount, goalsCount) => {
    cy.request({
      method: "GET",
      url: `${apiNew}/categories`,
      headers: {
        authorization: `Bearer ${token}`,
      },
    }).then((response) => {
      // Получение указанного количества случайных категорий
      let categoriesIndex = [];
      let count = categoryCount;
      while (count > 0) {
        let index = Math.floor(
          Math.random() * response.body.data.categories.length
        );
        if (!categoriesIndex.includes(index)) {
          categoriesIndex.push(index);
          count--;
        }
      }
      let categoryIds = categoriesIndex.map(
        (index) => response.body.data.categories[index].id
      );
      let categoriesNames = categoriesIndex.map(
        (index) => response.body.data.categories[index].name.ru
      );
      // Получаем все цели, либо указанное количество случайных целей из каждой категории
      let goalsIds = [];
      for (let i = 0; i < categoriesIndex.length; i++) {
        let category = response.body.data.categories[categoriesIndex[i]];
        let goals =
          goalsCount === "all"
            ? category.children
            : category.children.slice(0, goalsCount);
        goalsIds.push(...goals.map((goal) => goal.id));
      }
      let goalsNames = goalsIds.map(
        (goalId) =>
          response.body.data.categories
            .find((category) =>
              category.children.some((goal) => goal.id === goalId)
            )
            .children.find((goal) => goal.id === goalId).name.ru
      );
      return {
        categoryIds: categoryIds,
        goalsIds: goalsIds,
        categoriesNames: categoriesNames,
        goalsNames: goalsNames,
      };
    });
  }
);

//получение id курса/курсов по цели/целям.all - все курсы, если ничего не передавать - один курс
Cypress.Commands.add("getCourseFromGoal", (token, goalIds, coursesCount) => {
  // преобразовать goalIds в строку и вставить в URL запроса
  const categoryIdParam = Array.isArray(goalIds) ? goalIds.join(",") : goalIds;
  const url = `${apiNew}/courses?categoryId=${categoryIdParam}`;

  cy.request({
    method: "GET",
    url: url,
    headers: {
      authorization: `Bearer ${token}`,
    },
  }).then((response) => {
    // Получаем указанное количество случайных курсов
    let courseIds = [];
    let courseTitles = [];
    for (
      let i = 0;
      i < Math.min(coursesCount, response.body.data.courses.length);
      i++
    ) {
      let coursesIndex = Math.floor(
        Math.random() * response.body.data.courses.length
      );
      let courseId = response.body.data.courses[coursesIndex].id;
      let courseTitle = response.body.data.courses[coursesIndex].title;
      courseIds.push(courseId);
      courseTitles.push(courseTitle);
      response.body.data.courses.splice(coursesIndex, 1);
    }
    return { courseId: courseIds, courseTitle: courseTitles };
  });
});
