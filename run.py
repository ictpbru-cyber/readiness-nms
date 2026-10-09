#!/usr/bin/env python
"""
Readiness NMS Runner
Usage: python run.py
"""
import uvicorn
from app.config import settings

def main():
    print(f"==================================================")
    print(f"  Starting {settings.APP_NAME} (Python Edition)")
    print(f"  Access URL: http://{settings.HOST}:{settings.PORT}")
    print(f"  Admin User: admin / Password@123")
    print(f"==================================================")
    uvicorn.run("main:app", host=settings.HOST, port=settings.PORT, reload=settings.DEBUG)

if __name__ == "__main__":
    main()

