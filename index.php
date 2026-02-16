<?php
// ================== PHP BACKEND ==================
if(isset($_POST['message'])){

    $apiKey = "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"; // 🔐 Yaha apni API key daalo

    $message = $_POST['message'];

    $data = [
        "model" => "gpt-3.5-turbo",
        "messages" => [
            ["role" => "system", "content" => "Tum Nabu ho. Tum ek intelligent, friendly, soft-spoken female AI assistant ho. Hindi aur English mix me naturally baat karo. Short aur clear jawab do."],
            ["role" => "user", "content" => $message]
        ]
    ];

    $ch = curl_init("https://api.openai.com/v1/chat/completions");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer $apiKey"
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $response = curl_exec($ch);
    curl_close($ch);

    echo $response;
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>NABU AI Assistant</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif}

body{
  background:linear-gradient(160deg,#020617,#000814);
  color:#fff;
  height:100vh;
  display:flex;
  justify-content:center;
  align-items:center;
}

.app{
  width:100%;
  max-width:420px;
  height:100vh;
  display:flex;
  flex-direction:column;
  padding:18px;
}

.status{
  text-align:center;
  font-size:14px;
  opacity:.9;
  margin-top:10px;
}

.glass{
  flex:1;
  margin:15px 0;
  background:rgba(255,255,255,0.05);
  backdrop-filter:blur(12px);
  border-radius:20px;
  display:flex;
  justify-content:center;
  align-items:center;
  box-shadow:0 0 25px rgba(0,255,255,.15);
}

.orb{
  width:170px;
  height:170px;
  border-radius:50%;
  background:radial-gradient(circle,#00f5ff,#0061ff);
  box-shadow:0 0 40px #00f5ff;
  animation:idle 3s infinite;
}

.orb.thinking{animation:thinking 1.2s infinite}
.orb.speaking{animation:speaking .5s infinite}

@keyframes idle{50%{transform:scale(1.05)}}
@keyframes thinking{50%{box-shadow:0 0 55px #00f5ff}}
@keyframes speaking{50%{transform:scale(1.12)}}

.controls{
  display:flex;
  justify-content:space-around;
}

.btn{
  border:1px solid #00f5ff;
  background:transparent;
  color:#00f5ff;
  padding:8px 16px;
  border-radius:25px;
  cursor:pointer;
  transition:.3s;
}

.btn:hover{
  background:#00f5ff;
  color:#000;
  transform:scale(1.05);
}

.mic-wrap{
  display:flex;
  justify-content:center;
  margin-top:15px;
}

.mic{
  width:72px;
  height:72px;
  border-radius:50%;
  border:2px solid #00f5ff;
  display:flex;
  justify-content:center;
  align-items:center;
  font-size:26px;
  cursor:pointer;
  transition:.3s;
}

.mic:hover{
  box-shadow:0 0 30px #00f5ff;
  transform:scale(1.1);
}
</style>
</head>

<body>

<div class="app">
  <div class="status" id="status">Initializing Nabu...</div>

  <div class="glass">
    <div class="orb" id="orb"></div>
  </div>

  <div class="controls">
    <button class="btn" onclick="toggleVoice()">Voice</button>
    <button class="btn" onclick="resetNabu()">Reset</button>
  </div>

  <div class="mic-wrap">
    <div class="mic" onclick="manualListen()">🎤</div>
  </div>
</div>

<script>
let voiceOn = true;
const orb = document.getElementById("orb");
const statusEl = document.getElementById("status");

const SR = window.SpeechRecognition || window.webkitSpeechRecognition;
const recognition = new SR();
recognition.lang = "hi-IN";
recognition.continuous = false;

/* ========== WELCOME MESSAGE + AUTO MIC ========== */
window.onload = function(){
  setTimeout(()=>{
    speak("Namaste 😊 Nabu AI aapki kaise madad kar sakti hai?");
  },800);
};
/* ================================================= */

recognition.onresult = e => {
  const text = e.results[0][0].transcript;
  statusEl.innerText = "Aapne kaha: " + text;
  processAI(text);
};

recognition.onend = ()=>{
  setTimeout(()=>recognition.start(),1000);
};

function manualListen(){
  recognition.start();
}

function speak(text){
  if(!voiceOn) return;

  orb.className="orb speaking";

  const u = new SpeechSynthesisUtterance(text);
  u.lang="hi-IN";
  u.pitch=1.2;
  u.rate=0.95;

  speechSynthesis.speak(u);

  u.onend=()=>{
    orb.className="orb";
    recognition.start();
  };
}

function processAI(text){
  orb.className="orb thinking";

  fetch("index.php",{
    method:"POST",
    headers:{"Content-Type":"application/x-www-form-urlencoded"},
    body:"message="+encodeURIComponent(text)
  })
  .then(res=>res.json())
  .then(data=>{
    const reply = data.choices[0].message.content;
    speak(reply);
  })
  .catch(()=>{
    speak("Network issue aa raha hai, ek baar fir try kariye 😊");
  });
}

function toggleVoice(){
  voiceOn=!voiceOn;
  speak(voiceOn?"Voice on":"Voice off");
}

function resetNabu(){
  speak("System reset ho gaya 💙");
}
</script>

</body>
</html>