// firebase-messaging-sw.js
importScripts('https://www.gstatic.com/firebasejs/9.6.1/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/9.6.1/firebase-messaging-compat.js');

firebase.initializeApp({
  apiKey: "AIzaSyAkeqjE_GTnKqnXwnmstsjH7DezknpFSE",
  authDomain: "buku-tamu-44225.firebaseapp.com",
  projectId: "buku-tamu-44225",
  messagingSenderId: "105863260105",
  appId: "1:105863260105:web:6a4ab69e8cc43103c2aa18",
  measurementId: "G-S1GB57JDG6"
});

const messaging = firebase.messaging();

messaging.onBackgroundMessage(function(payload) {
  console.log('[firebase-messaging-sw.js] Received background message ', payload);

  const notificationTitle = payload.notification.title;
  const notificationOptions = {
    body: payload.notification.body,
    icon: 'logo-smkn1slawi1.png' 
  };

  self.registration.showNotification(notificationTitle, notificationOptions);
});
