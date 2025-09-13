<?php
// Simple test page for confirmation modal
$page_title = 'Test Confirmation Modal';
include 'includes/layout_start.php';
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>Confirmation Modal Test</h4>
            </div>
            <div class="card-body">
                <p>Click the buttons below to test the confirmation modal functionality:</p>
                
                <div class="btn-group-vertical d-block">
                    <button type="button" class="btn btn-primary mb-2" onclick="testBasicConfirm()">
                        Test Basic Confirm
                    </button>
                    
                    <button type="button" class="btn btn-success mb-2" onclick="testApproveAction()">
                        Test Approve Action
                    </button>
                    
                    <button type="button" class="btn btn-danger mb-2" onclick="testDeleteAction()">
                        Test Delete Action
                    </button>
                    
                    <button type="button" class="btn btn-warning mb-2" onclick="testWarningAction()">
                        Test Warning Action
                    </button>
                    
                    <button type="button" class="btn btn-info mb-2" onclick="testAlert()">
                        Test Alert Modal
                    </button>
                    
                    <form method="POST" class="d-inline">
                        <input type="hidden" name="test_action" value="form_submit">
                        <button type="submit" class="btn btn-secondary mb-2" onclick="return testFormSubmit(this.form)">
                            Test Form Submission
                        </button>
                    </form>
                </div>
                
                <div id="testResults" class="mt-4">
                    <h5>Test Results:</h5>
                    <div id="resultList" class="alert alert-info">
                        No tests performed yet.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function addResult(message) {
    const resultList = document.getElementById('resultList');
    const timestamp = new Date().toLocaleTimeString();
    resultList.innerHTML = `<div><strong>${timestamp}:</strong> ${message}</div>` + resultList.innerHTML;
    resultList.className = 'alert alert-success';
}

function testBasicConfirm() {
    window.confirmModal.confirm('This is a basic confirmation test. Do you want to proceed?', function(confirmed) {
        if (confirmed) {
            addResult('Basic confirm: User clicked CONFIRM');
        } else {
            addResult('Basic confirm: User clicked CANCEL or closed modal');
        }
    });
}

function testApproveAction() {
    window.confirmAction(
        'Are you sure you want to approve this test item?',
        function() {
            addResult('Approve action: User CONFIRMED approval');
        },
        function() {
            addResult('Approve action: User CANCELLED approval');
        },
        {
            title: 'Approve Test Item',
            confirmText: 'Approve',
            confirmClass: 'btn-success'
        }
    );
}

function testDeleteAction() {
    window.confirmDelete(
        'Test Item #123',
        function() {
            addResult('Delete action: User CONFIRMED deletion of Test Item #123');
        },
        function() {
            addResult('Delete action: User CANCELLED deletion');
        }
    );
}

function testWarningAction() {
    window.confirmStatus(
        'Suspend',
        'Test Account',
        function() {
            addResult('Status action: User CONFIRMED suspension of Test Account');
        },
        function() {
            addResult('Status action: User CANCELLED suspension');
        }
    );
}

function testAlert() {
    window.confirmModal.alert('This is a test alert message!', function() {
        addResult('Alert: User clicked OK');
    }, {
        title: 'Test Alert',
        okText: 'Got it!'
    });
}

function testFormSubmit(form) {
    window.confirmModal.confirm('Are you sure you want to submit this form?', function(confirmed) {
        if (confirmed) {
            addResult('Form submission: User CONFIRMED - Form would be submitted');
            // Note: We're not actually submitting to prevent page reload
        } else {
            addResult('Form submission: User CANCELLED');
        }
    }, {
        title: 'Confirm Form Submission',
        confirmText: 'Submit',
        confirmClass: 'btn-primary'
    });
    
    return false; // Prevent actual form submission for testing
}
</script>

<?php include 'includes/layout_end.php'; ?>