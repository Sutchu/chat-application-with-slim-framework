**About**
This will be a basic chat application backend using Slim Framework 4 and Sqlite3.

**Initial Plan for models and actions**

*MODELS*

    AuthToken:
        - id: primary_key
        - user: foreign key
        - token
        - expires_at
    
    User:
        - id: primary_key
        - username: unique
        - email
        - created_at
        - password_hash


    Chat:
        - id: primary_key
        - user1
        - user2

        - unique together: user1, user2

    Message:
        - id: primary_key
        - chat: Chat foreign key
        - sender: userforeign key
        - content
        - created_at
        - is_seen
        - reply_to?: Message foreign key

*ENDPOINTS*

    register - POST - register/
        - username
        - password

        - returns ok

    login - POST - login/
        - username
        - password

        - returns auth_token

    listChats - GET - chat/
        - Returns paginated list of chats

    getChat - GET - chat/{username}
        - Returns paginated list of messages

    sendMessage - POST chat/{username}/message
        - message_content
        - reply_to?: message_id

        - Returns generated message_id

    markMessageAsSeen - POST - chat/{username}/mark-as-seen
        - Returns ok

    deleteMessage - DELETE - chat/{username}/message/{message_id}
        - Returns ok
