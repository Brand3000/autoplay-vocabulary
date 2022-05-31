export const ajaxWaitPutRequest = async (url, data = {}) => {
  try {
    await fetch(url, {
      method: "PUT",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(data),
    });
  } catch (error) {
    console.error(error);
    throw(new Error("Some error with the internet connection.<br>Press a tab name on the top"));//if internet lost
  }
};
