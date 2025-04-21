// Floating Chat Icon
const container = document.getElementById("jeevsutra-chatbot-container");
container.innerHTML = `
  <div id="jeevsutra-chat-icon"></div>
  <div id="jeevsutra-chat-window">
    <div id="jeevsutra-chat-header">${JeevsutraBot.bot_name}</div>
    <div id="jeevsutra-chat-body"></div>
    <div id="jeevsutra-chat-input">
      <input type="text" placeholder="Type here..."/>
      <button>➤</button>
      <button id="jeevsutra-mic-btn">🎙️</button>
      <input type="file" id="jeevsutra-upload" style="display:none;" accept=".jpg,.jpeg,.png,.pdf,.webp" />
      <label for="jeevsutra-upload" style="cursor:pointer;">📎</label>
    </div>
  </div>
`;

document.getElementById("jeevsutra-chat-icon").addEventListener("click", function () {
  const win = document.getElementById("jeevsutra-chat-window");
  win.style.display = win.style.display === "flex" ? "none" : "flex";
});

document.querySelector("#jeevsutra-chat-input button").addEventListener("click", function () {
  const inputField = document.querySelector("#jeevsutra-chat-input input");
  const message = inputField.value.trim();
  if (!message) return;

  const body = document.getElementById("jeevsutra-chat-body");
  body.innerHTML += `<div><b>🧍 You:</b> ${message}</div>`;
  inputField.value = "";

  fetch(JeevsutraBot.ajax_url, {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: new URLSearchParams({
      action: 'jeevsutra_chatbot_ask',
      message: message,
      lang: JeevsutraBot.lang_default
    })
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      body.innerHTML += `<div><b>🤖 ${JeevsutraBot.bot_name}:</b> ${data.data.reply}</div>`;
      if (data.data.redirect) {
        setTimeout(() => {
          window.open(data.data.redirect, "_blank");
        }, 1500);
      }
    } else {
      body.innerHTML += `<div><i>Error: ${data.data}</i></div>`;
    }
    body.scrollTop = body.scrollHeight;
  });
});

// Upload File
document.getElementById("jeevsutra-upload").addEventListener("change", function (e) {
  const file = e.target.files[0];
  if (!file) return;

  const formData = new FormData();
  formData.append('action', 'jeevsutra_file_upload');
  formData.append('file', file);

  fetch(JeevsutraBot.ajax_url, {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    const body = document.getElementById("jeevsutra-chat-body");
    if (data.success) {
      body.innerHTML += `<div><b>📎 You uploaded:</b> ${file.name}</div>`;
      body.innerHTML += `<div><b>🤖 ${JeevsutraBot.bot_name}:</b> ${data.data.message}</div>`;
    } else {
      body.innerHTML += `<div><b>⚠️ Error:</b> ${data.data.message}</div>`;
    }
    body.scrollTop = body.scrollHeight;
  });
});

// Voice Input
const micBtn = document.getElementById("jeevsutra-mic-btn");
const inputField = document.querySelector("#jeevsutra-chat-input input");
if (window.SpeechRecognition || window.webkitSpeechRecognition) {
  const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
  const recog = new SpeechRecognition();
  recog.lang = 'bn-IN';

  micBtn.onclick = () => {
    recog.start();
  };
  recog.onresult = (event) => {
    inputField.value = event.results[0][0].transcript;
  };
}
