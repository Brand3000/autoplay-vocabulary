//function for setting a system message
export function useSetSystemMessage(newMessage = "") {
  document.getElementById("message").innerHTML = newMessage;
}
