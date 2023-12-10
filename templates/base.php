<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" href="https://cvsu.edu.ph/wp-content/uploads/2018/01/CvSU-logo-trans.png" sizes="192x192">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="{{ url_for('static', filename='style.css') }}">
   

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>CvSU Online Student Admission System</title>
</head>
<body>
    
    <nav class="navbar navbar-expand-md navbar-dark fixed-top" style="background-color: #376d1d;">
        <a class="navbar-brand" href="#">CvSU OSAS</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault"
                aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarsExampleDefault">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Admission Result</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Log-out</a>
                </li>
            </ul>
            <form class="form-inline my-2 my-lg-0">
            </form>
        </div>
    </nav>
    <button type="button" style="display: none;" class="btn btn-primary" id="displayok" data-toggle="modal" data-target="#modalOK">
        Launch demo modal
    </button>
    <!-- Modal -->
    <div class="modal fade" id="modalOK" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">CvSU Online Student Admission System</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <span id='modaldisplaytext'></span>
                    <input type="hidden" name="formid" id="formid">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="movewindow()">OK</button>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="chatbox resizable">
            <div class="chatbox__support">
                <div class="chatbox__header">
                    <div class="chatbox__image--header">
                        <img src="static/images/CvSU-logo-trans.png" alt="image" width="50" height="40">
                    </div>
                    <div class="chatbox__content--header">
                        <h4 class="chatbox__heading--header">Chat support</h4>
                        <p class="chatbox__description--header">CVSU ADMISSION RESPONSE SYSTEM</p>
                    </div>
                </div>
                <div class="chatbox__messages">
                    <div></div>
                </div>
                <div class="chatbox__footer">
                    <button class="feedback-button" id="toggleFeedbackForm" style="font-size: 12px; padding: 4px 8px;">
                        <i class="fas sm fa-paperclip"></i> Feedback
                    </button>
                    <input type="text" placeholder="Write a message..." style=" font-size: 16px; padding: 12px;">
                    <button class="chatbox__send--footer send__button" id="sendMessageButton" style="font-size: 12px; padding: 4px 8px;">
                        <i class="fas sm fa-paper-plane"></i> Send
                    </button>
                   
                </div>
                
                <div class="chatbox__feedback">
                    <div id="feedbackForm" style="display: none;">
                     <form action="/save_feedback" method="POST">
                         <div class="form-group">
                              <label for="feedbackMessage">Feedback Message</label>
                              <textarea placeholder="Write a feedback..." class="form-control" id="message" name="feedmessage" rows="3" required></textarea>
                          </div>
                            <button type="submit" class="btn btn-success"><i class="fas sm fa-paper-plane"></i> Submit Feedback</button>
                     </form>
                    </div>
                </div>
            </div>
            <div class="chatbox__button">
                <button id="chatboxButton"><img src="static/images/CvSU-logo-trans.png" width="50" height="40" /></button>
            </div>
        </div>
    </div>
    <script>
    var $SCRIPT_ROOT = "{{ request.script_root|tojson }}";
    document.getElementById('toggleFeedbackForm').addEventListener('click', function() {
        var feedbackForm = document.getElementById('feedbackForm');
        feedbackForm.style.display = (feedbackForm.style.display === 'none') ? 'block' : 'none';
    });

    $(document).ready(function () {
        $(".resizable").resizable({
            minHeight: 300,
            minWidth: 300,
            handles: 'se',  // Resize from the bottom-right corner
        });

        $(".chatbox").draggable({
            handle: ".chatbox__header", // Make the header the handle for dragging
            containment: "body", // Ensure the chatbox stays within the boundaries of the body
        });
    });
</script>

    </script>
    <script>
    var feedbackForm = document.getElementById('feedbackForm');
    var feedbackButton = document.getElementById('toggleFeedbackForm');
    feedbackButton.addEventListener('click', function () {
        if (feedbackForm.style.display === 'none' || feedbackForm.style.display === '') {
            feedbackForm.style.display = 'block';
        } else {
            feedbackForm.style.display = 'none';
        }
    });

    // Add event listener for the chatbox button
    var chatboxButton = document.getElementById('chatboxButton');
    chatboxButton.addEventListener('click', function () {
        // Display a window alert
        window.alert("Remember: Your chat will not be saved and will be lost upon reloading.");
    });
</script>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
            crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"
            integrity="sha384-rVcXteIEv0nA3P0ARgkp/5RXUN2WRBs/w1c/2d6Oz1As5oGUaOs3lsOKXtjBsEq"
            crossorigin="anonymous"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script type="text/javascript" src="{{ url_for('static', filename='app.js') }}"></script>
    
</body>
</html>
