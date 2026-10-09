# Multi-stage Dockerfile for Readiness NMS 2.0
FROM python:3.11-slim

# Prevent Python from writing .pyc and enable unbuffered output
ENV PYTHONDONTWRITEBYTECODE=1
ENV PYTHONUNBUFFERED=1

# Install system dependencies (including ping and network tools)
RUN apt-get update && apt-get install -y --no-install-recommends \
    iputils-ping \
    curl \
    gcc \
    libsnmp-dev \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

# Copy dependency definitions
COPY requirements.txt .

# Install python dependencies
RUN pip install --no-cache-dir -r requirements.txt

# Copy application source code
COPY . .

# Expose web service port
EXPOSE 8000

# Default environment variables
ENV HOST=0.0.0.0
ENV PORT=8000

# Run entrypoint
CMD ["python", "run.py"]
