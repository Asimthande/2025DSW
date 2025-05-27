let hide_=document.getElementById("splash-screen");
setTimeout(2000,function(){
    hide_.style.display="none";
});

let busX = -200;
let busStopped = false;
let students = [];
let busLeaving = false;
let atCampus = false;
let trafficLightState = "green";
let lastLightSwitch = 0;

let residenceX = 50;
let portX = 300;
let campusX = window.innerWidth - 250;
let trafficLightX = 600;

let conversationBoxVisible = false;
let conversationText = "";
let typingIndex = 0;
let messageSent = false;

let studentsVisible = false;

function setup() {
  let busStopSection = document.querySelector('.bus-stop-animation');
  let canvas = createCanvas(busStopSection.offsetWidth, 400);
  canvas.parent(busStopSection);
  textSize(16);

  for (let i = 0; i < 3; i++) {
    students.push({
      x: residenceX + 50 - i * 30,
      y: 270,
      visible: false,
      phoneOut: false,
      walking: false,
      boarded: false,
      dropped: false,
      waving: false,
      insideCampus: false,
      phoneTimer: 0,
      dropTimer: 0,
      shirtColor: color(random(100, 255), random(100, 255), random(100, 255)),
      campusX: campusX + i * 20,
      campusY: 270
    });
  }

  lastLightSwitch = millis();
}

function draw() {
  background(220);
  drawSky();
  drawRoad();
  drawTrafficLights();
  drawHouse();
  drawBusPort();
  drawCampus();
  drawBus(busX);

  if (millis() - lastLightSwitch > 4000) {
    trafficLightState = trafficLightState === "green" ? "red" : "green";
    lastLightSwitch = millis();
  }

  if (!busStopped && busX < portX - 80) {
    busX += 2;
  } else if (!busStopped && busX >= portX - 80) {
    busStopped = true;

    setTimeout(() => {
      conversationBoxVisible = true;
      messageSent = true;

      setTimeout(() => {
        studentsVisible = true;
        students.forEach(s => {
          s.visible = true;
          s.walking = true;
        });
      }, 3000);
    }, 1500);
  }

  if (conversationBoxVisible && messageSent) {
    if (millis() > lastLightSwitch + 3000 && typingIndex < "Ready to depart.".length) {
      typingIndex++;
      conversationText = "Ready to depart.".substring(0, typingIndex);
    }
  }

  students.forEach((s, index) => {
    if (studentsVisible && s.visible && !s.boarded && !s.dropped) {
      if (s.walking && s.x < busX + 60 + index * 20) {
        s.x += 1.5;
      } else if (s.walking) {
        s.walking = false;
        s.boarded = true;

        if (students.every(st => st.boarded)) {
          messageSent = false;
          busLeaving = true;
        }
      }

      if (s.walking) {
        drawStickFigureWalking(s.x, s.y, s.shirtColor);
      } else {
        drawStickFigure(s.x, s.y, s.shirtColor, false);
      }
    }
  });

  if (busLeaving && busX < campusX - 150) {
    busX += 3;
  } else if (busLeaving && !atCampus && busX >= campusX - 150) {
    atCampus = true;

    students.forEach((s, i) => {
      s.boarded = false;
      s.dropped = true;
      s.visible = true;
      s.walking = false;
      s.x = busX + 40 + i * 20;
      s.y = 270;
      s.waving = true;
      s.dropTimer = millis();
    });
  }

  students.forEach((s) => {
    if (s.dropped && !s.insideCampus) {
      if (s.waving && millis() > s.dropTimer + 1500) {
        s.waving = false;
        s.walking = true;
      }

      if (s.walking) {
        if (s.x < s.campusX) s.x += 1.2;
        if (s.y > s.campusY - 30) s.y -= 0.5;
        drawStickFigure(s.x, s.y, s.shirtColor, true);
        if (s.x >= s.campusX && s.y <= s.campusY - 30) s.insideCampus = true;
      } else if (s.waving) {
        drawStickFigureWaving(s.x, s.y, s.shirtColor);
      }
    }
  });

  if (atCampus && busX < width + 200) {
    busX += 3;
  }

  if (conversationBoxVisible && messageSent) {
    fill(255);
    rect(busX + 20, 190, 200, 60, 10);
    fill(0);
    textSize(14);
    text(conversationText, busX + 30, 220);
  }
}

function windowResized() {
  resizeCanvas(windowWidth, 400);
}

function drawSky() {
  for (let y = 0; y < height; y++) {
    let inter = map(y, 0, height, 0, 1);
    let c = lerpColor(color(135, 206, 250), color(255), inter);
    stroke(c);
    line(0, y, width, y);
  }
  noStroke();
  fill(255, 255, 0);
  ellipse(80, 80, 60, 60);
}

function drawRoad() {
  fill(200);
  rect(0, 300, width, 100);
  stroke(255);
  for (let i = 0; i < width; i += 40) {
    line(i, 350, i + 20, 350);
  }
}

function drawTrafficLights() {
  fill(50);
  rect(trafficLightX, 200, 10, 100);
  fill(30);
  rect(trafficLightX - 10, 170, 30, 60);

  fill(trafficLightState === "red" ? "red" : "darkred");
  ellipse(trafficLightX + 5, 180, 12);
  fill("orange");
  ellipse(trafficLightX + 5, 200, 12);
  fill(trafficLightState === "green" ? "green" : "darkgreen");
  ellipse(trafficLightX + 5, 220, 12);
}

function drawHouse() {
  fill(180, 140, 100);
  rect(residenceX, 180, 100, 120);
  fill(0);
  text("Student Residence", residenceX, 175);
  fill(100);
  rect(residenceX + 40, 250, 20, 50);
}

function drawBusPort() {
  fill(150);
  rect(portX, 220, 100, 80, 10);
  fill(0);
  text("Stabus Port", portX + 10, 215);
}

function drawCampus() {
  fill(100, 100, 255);
  rect(campusX, 100, 250, 200);
  fill(0);
  text("Campus", campusX + 10, 130);
  fill(180);
  rect(campusX, 250, 250, 10);
}

function drawBus(x) {
  fill(255);
  rect(x, 230, 160, 60, 10);
  fill(120);
  rect(x + 20, 240, 30, 20, 3);
  rect(x + 60, 240, 30, 20, 3);
  rect(x + 100, 240, 30, 20, 3);
  fill(0);
  ellipse(x + 30, 290, 20, 20);
  ellipse(x + 130, 290, 20, 20);
  fill(255, 200, 0);
  rect(x + 140, 230, 20, 30, 5);
  fill(200, 0, 0);
  text("Stabus", x + 50, 275);
}

function drawStickFigure(x, y, shirtColor, isWalking = false) {
  stroke(0);
  strokeWeight(2);
  fill(shirtColor);
  ellipse(x, y - 30, 20, 20);
  line(x, y - 20, x, y + 10);
  line(x, y - 10, x - 10, y);
  line(x, y - 10, x + 10, y);

  let legOffset = isWalking ? sin(frameCount * 0.2) * 7 : 0;
  line(x, y + 10, x - 10 + legOffset, y + 30);
  line(x, y + 10, x + 10 - legOffset, y + 30);
}

function drawStickFigureWalking(x, y, shirtColor) {
  stroke(0);
  strokeWeight(2);
  fill(shirtColor);
  ellipse(x, y - 30, 20, 20);
  line(x, y - 20, x, y + 10);
  line(x, y - 10, x - 10, y);
  line(x, y - 10, x + 10, y);

  let legOffset = sin(frameCount * 0.2) * 7;
  line(x, y + 10, x - 10 + legOffset, y + 30);
  line(x, y + 10, x + 10 - legOffset, y + 30);
}

function drawStickFigureWaving(x, y, shirtColor) {
  stroke(0);
  strokeWeight(2);
  fill(shirtColor);
  ellipse(x, y - 30, 20, 20);
  line(x, y - 20, x, y + 10);
  line(x, y - 10, x - 10, y);
  let waveY = y - 10 + sin(frameCount * 0.3) * 5;
  line(x, y - 10, x + 10, waveY);
  line(x, y + 10, x - 10, y + 30);
  line(x, y + 10, x + 10, y + 30);
}
