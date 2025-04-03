let user_type=document.getElementById("user-type");
let login=document.getElementById('submit');
function UserType(){
    switch(user_type.value){
        case 'Student':{
            login.setAttribute("onclick","window.location.href='dashboard.php'");
            break;
        }
        case 'Driver':{
            login.setAttribute("onclick","window.location.href='driver.php'");
            break;
        }
        case 'Admin':{
            login.setAttribute("onclick","window.location.href='admin.php'");
            break;
        }
        default:{
            window.alert("Select User Type");
        }
    }
}