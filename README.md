**About**
This will be a basic chat application backend using Slim Framework 4 and Sqlite3.

**Initial Plan for models and actions**

*MODELS*

User:
- Nickname: unique
- id: primary_key
- Mail?

Chat:
- User1
- User2

- unique together: User1, User2

Message:
- Chat: foreign key
- Sender: foreign key
- Content
- Date
- isSeen

*ENDPOINTS*

register - POST - register/
- Username
- Password

login - POST - login/
- Username
- Password

listChats - GET - chat/
- returns: paginated list of chats

getChat - GET - chat/{username}
- returns: paginated list of messages

sendMessage - POST chat/{username}/message
- message_content

- Returns generated message_id

markMessageAsSeen - POST - chat/{username}/message/{id}/mark-as-seen
- message_id

- Returns ok
