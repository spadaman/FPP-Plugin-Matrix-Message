# FPP-Plugin-Matrix-Message

Forked from FalconChristmas repo.

Changes added to the following repos to support some extra features.
* FPP-Plugin-Matrix-Message
* FPP-Plugin-MessageQueue
* FPP-Plugin-TwilioControl


## Extra Features
You can configure how many messages/SMS' per phone number can be waiting in the queue for display. This is to stop a single number filling up your queue.
* Set via MAX_PENDING_MSG_PER_NUMBER on the Twilio plugin setup page.

You can set how many messages are played per run, so you can add the RUN-MATRIX.sh script into your playlist and know only up to 'x' messages will run each time the script is run.
* Set via MAX_MESSAGES_PER_RUN on the MatrixMessage plugin setup page.

You can set an effect sequence file that will run at start of each message being displayed, so you can have the text scroll across a nice backdrop.
* Set via EFFECT_FOR_DISPLAY on the MatrixMessage plugin setup page. Beware: Case sensitive!