export const ajaxWaitFetchRequest = async (url, data = {}) => {
  try {
    let response = await fetch(url, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(data),
    });
    return await response.json();
  } catch (error) {
    console.error(error.message);
    throw(new Error("Some error with the internet connection.<br>Press a tab name on the top"));//if internet lost
  }
};
