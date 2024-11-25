<script>
    let localStream;
let remoteStream;
let peerConnection;
const serverConfig = {
  iceServers: [
    { urls: 'stun:stun.l.google.com:19302' } // Use Google's STUN server
  ]
};

// Get media stream (audio/video)
navigator.mediaDevices.getUserMedia({ video: true, audio: true })
  .then(stream => {
    localStream = stream;
    // Display local video in a video element (or hidden)
    document.getElementById('localVideo').srcObject = stream;
  })
  .catch(error => console.error('Error accessing media devices.', error));

// Start a call
document.getElementById('makeCall').addEventListener('click', () => {
  initiateCall();
});

function initiateCall() {
  peerConnection = new RTCPeerConnection(serverConfig);
  localStream.getTracks().forEach(track => peerConnection.addTrack(track, localStream));

  // Listen for remote stream
  peerConnection.ontrack = (event) => {
    remoteStream = event.streams[0];
    document.getElementById('remoteVideo').srcObject = remoteStream;
  };

  // Create an offer and send to the other user
  peerConnection.createOffer()
    .then(offer => {
      return peerConnection.setLocalDescription(offer);
    })
    .then(() => {
      // Send offer to the other user using your signaling server
      // Example: sendSignal('offer', peerConnection.localDescription);
    })
    .catch(error => console.error('Error creating offer: ', error));
}

// Answer a call
document.getElementById('answerCall').addEventListener('click', () => {
  answerCall();
});

function answerCall() {
  peerConnection.setRemoteDescription(new RTCSessionDescription(callOffer)); // Received offer from caller
  localStream.getTracks().forEach(track => peerConnection.addTrack(track, localStream));
  
  peerConnection.createAnswer()
    .then(answer => {
      return peerConnection.setLocalDescription(answer);
    })
    .then(() => {
      // Send answer to the caller using signaling server
      // Example: sendSignal('answer', peerConnection.localDescription);
    })
    .catch(error => console.error('Error answering call: ', error));
}

// Reject a call
document.getElementById('rejectCall').addEventListener('click', () => {
  rejectCall();
});

function rejectCall() {
  // Close the connection
  peerConnection.close();
  document.getElementById('callInterface').style.display = 'none';
}

// Handling incoming calls - When you receive a call, display the call UI
function receiveCall(callOffer) {
  // Store the offer for later use
  window.callOffer = callOffer;
  document.getElementById('callInterface').style.display = 'block';
}

// Send signal (you need to implement this part for your server)
function sendSignal(type, data) {
  // Example: send via WebSocket or any other signaling mechanism
  // websocket.send(JSON.stringify({ type, data }));
}

</script>