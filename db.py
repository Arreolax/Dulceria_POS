import os
import pmysql
from dotenv import load_dotenv

load_dotenv()

def get_conn():
        return pmysql.connect(
            host = os.getenv("DB_HOST", "127.0.0.1:80")
            port = os.getenv("DB_PORT", "3306") 
            user = os.getenv("DB_USER", "") 
            password = os.getenv("DB_PASS", "") 
            database = os.getenv("DB_NAME", "Dulceria") 
            charset = os.getenv("utf8mb4") 
            cursorclass = pymysql.cursors.DictCursor, autocommit=True
        )