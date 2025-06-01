const chatbox = document.getElementById("chatbox");
const userInput = document.getElementById("userInput");

const knowledgeBase = [
  { keywords: ["problem", "issue", "challenge", "gap"], answer: "The problem is that the current UJ shuttle timetable is unreliable, causing delays, missed classes, overcrowding, and student frustration." },
  { keywords: ["solution", "feature", "how", "works", "address"], answer: "UJ Stabus offers real-time bus location tracking, dynamic timetable updates, push notifications, bus capacity monitoring, peak time insights, emergency alerts, and campus-specific routing for SWC, APB, APK, and DFC." },
  { keywords: ["technology", "tech", "tools", "stack"], answer: "The system uses HTML, CSS, JavaScript for the frontend, GPS and real-time tracking hardware, backend APIs, cloud-based databases, and push notification services." },
  { keywords: ["outcome", "benefit", "result", "expected"], answer: "Expected outcomes include reduced wait times, less overcrowding, improved student satisfaction, stress reduction, and a more efficient, predictable shuttle system." },
  { keywords: ["erd", "database", "schema", "data", "model"], answer: "The ERD includes entities like User, Bus, Driver, Route, Stop, Schedule, Real-Time Location, Notification, Reservation, Maintenance, Feedback, and Statistics to keep the system well-structured." },
  { keywords: ["notification", "push", "alert", "message"], answer: "Push notifications inform students in real time when their shuttle is approaching, delayed, canceled, or if emergencies occur." },
  { keywords: ["capacity", "full", "crowd", "overcrowd"], answer: "Bus capacity monitoring lets students see how full each shuttle is in real time, helping them avoid overcrowded buses." },
  { keywords: ["peak", "busy", "hours", "insights", "time"], answer: "Peak time insights analyze historical usage to predict busy periods, helping students plan trips during less crowded times." },
  { keywords: ["emergency", "breakdown", "delay", "detour", "alert"], answer: "Emergency alerts notify students immediately about shuttle breakdowns, detours, or significant delays, allowing them to make alternate plans." },
  { keywords: ["weather", "forecast", "conditions"], answer: "Integrated weather updates help students prepare for travel by showing current conditions and forecasts for each campus." },
  { keywords: ["account", "profile", "update", "settings", "user"], answer: "Users can manage and update their account information, including contact details, preferred routes, and notification settings." },
  { keywords: ["maintenance", "repair", "service", "status"], answer: "Drivers and admins can log and monitor bus maintenance schedules and issues, keeping the fleet in good condition and ensuring safety." },
  { keywords: ["question", "upload", "feedback", "help", "support"], answer: "Students can upload questions, report issues, or send feedback directly through the app for quick support or future improvements." },
  { keywords: ["stats", "analytics", "data", "dashboard", "monitor"], answer: "The platform offers visual dashboards and analytics showing shuttle usage trends, delays, satisfaction levels, and other key metrics." },
  { keywords: ["geo-fencing", "boundaries", "zones"], answer: "Geo-fencing allows the system to define virtual boundaries for each campus. Alerts are triggered if a bus enters or exits these zones unexpectedly." },
  { keywords: ["route optimization", "efficiency", "planning"], answer: "The system analyzes traffic patterns and student locations to suggest the most efficient routes, reducing travel time and fuel consumption." },
  { keywords: ["driver behavior", "monitoring", "safety"], answer: "Driver behavior is monitored for speed, braking patterns, and adherence to routes, ensuring safety and compliance with regulations." },
  { keywords: ["attendance", "RFID", "check-in", "boarding"], answer: "Students can check in using RFID cards or biometric systems, automatically marking their attendance and ensuring accurate records." },
  { keywords: ["live streaming", "cameras", "monitoring"], answer: "Cameras inside buses allow for live streaming, enabling real-time monitoring of student behavior and ensuring safety." },
  { keywords: ["trip history", "logs", "records"], answer: "The system maintains detailed logs of all trips, including routes taken, stops made, and times, for accountability and analysis." },
  { keywords: ["fuel management", "consumption", "efficiency"], answer: "Fuel consumption is tracked to identify inefficiencies, helping to reduce costs and environmental impact." },
  { keywords: ["driver training", "certification", "compliance"], answer: "Drivers undergo regular training and certification to ensure they are up-to-date with safety protocols and regulations." },
  { keywords: ["fleet management", "vehicles", "maintenance"], answer: "The system allows for comprehensive fleet management, including vehicle maintenance schedules, inspections, and replacements." },
  { keywords: ["reporting", "alerts", "notifications"], answer: "Customizable reporting and alert systems notify administrators of any anomalies or issues requiring attention." },
  {
  keywords: ["track", "bus", "location", "where"],
  answer: "To track a bus in real-time, open the app’s bus tracking feature, select your desired route or bus number, and view its current location on the map."
},
{
  keywords: ["check", "weather", "forecast", "conditions"],
  answer: "To check the weather, navigate to the weather section in the app where you’ll see current conditions and a detailed forecast for each campus."
},
{
  keywords: ["update", "information", "profile", "account", "edit"],
  answer: "To update your information, go to your account settings, select the fields you want to change such as contact details or preferred routes, and save the changes."
},
{
  keywords: ["delete", "account", "remove", "close"],
  answer: "To delete your account, visit the account settings, select 'Delete Account,' confirm your decision, and your profile along with data will be permanently removed."
},
{
  keywords: ["schedule", "view", "timetable", "see"],
  answer: "To see the shuttle schedule, open the schedule tab, select your campus and route, and browse the available departure and arrival times."
},
{
  keywords: ["see", "bus", "vehicle", "which", "ride"],
  answer: "To see your assigned bus, check the active route section where your current bus number and estimated arrival time are displayed."
},
{
  keywords: ["request", "specific", "bus", "book", "reserve"],
  answer: "To request a specific bus, use the booking feature, select your preferred bus if available, and submit your request for approval."
},
{
  keywords: ["filter", "schedule", "view", "sort", "search"],
  answer: "To filter the schedule, use the filter options such as time, route, or bus type in the schedule tab to narrow down your results."
},
{
  keywords: ["view", "bookings", "my", "reservations"],
  answer: "To view your bookings, go to the bookings section where all your current and past reservations are listed."
},
{
  keywords: ["cancel", "booking", "reservation", "remove"],
  answer: "To cancel a booking, open your bookings list, select the reservation you want to cancel, and confirm the cancellation."
},
{
  keywords: ["send", "emergency", "alert", "notify"],
  answer: "To send an emergency alert, access the emergency feature, select the type of emergency, provide necessary details, and submit to notify relevant authorities."
},
{
  keywords: ["maintenance", "view", "buses", "service", "status"],
  answer: "To view buses under maintenance, go to the maintenance dashboard where buses currently undergoing repairs or servicing are listed with status updates."
}

];

const fallbackAnswer = "Sorry, I don't have an answer for that question about UJ Stabus right now.";

const userIcon = "images/User.png";
const botIcon = "images/Chatbot Chat Message.jpg";

const greetings = ["hello", "hi", "hey", "greetings"];
const greetingResponse = "Hi there! Ask me anything about the UJ Stabus project.";

function sendMessage() {
  const message = userInput.value.trim();
  if (!message) return;

  appendMessage(message, "user-message", userIcon);
  userInput.value = "";

  const answer = getAnswer(message);

  setTimeout(() => {
    appendMessage(answer, "bot-message", botIcon);
  }, 500);
}

function appendMessage(text, className, avatarUrl) {
  const msgDiv = document.createElement("div");
  msgDiv.classList.add("message", className);

  const avatarDiv = document.createElement("div");
  avatarDiv.classList.add("avatar");
  avatarDiv.style.backgroundImage = `url('${avatarUrl}')`;

  const bubbleDiv = document.createElement("div");
  bubbleDiv.classList.add("bubble");
  bubbleDiv.textContent = text;

  msgDiv.appendChild(avatarDiv);
  msgDiv.appendChild(bubbleDiv);

  chatbox.appendChild(msgDiv);
  chatbox.scrollTop = chatbox.scrollHeight;
}

function getAnswer(userInputText) {
  const input = userInputText.toLowerCase();

  if (greetings.some(g => input.includes(g))) {
    return greetingResponse;
  }

  const matchedAnswers = new Set();

  for (const item of knowledgeBase) {
    for (const keyword of item.keywords) {
      if (input.includes(keyword)) {
        matchedAnswers.add(item.answer);
        break;
      }
    }
  }

  if (matchedAnswers.size > 0) {
    return Array.from(matchedAnswers).join("\n\n");
  }

  return fallbackAnswer;
}
