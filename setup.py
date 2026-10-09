from setuptools import setup, find_packages

with open("README.md", "r", encoding="utf-8") as fh:
    long_description = fh.read()

requirements = [
    "fastapi>=0.110.0",
    "uvicorn>=0.28.0",
    "sqlalchemy>=2.0.0",
    "bcrypt>=4.1.0",
    "itsdangerous>=2.1.0",
    "python-multipart>=0.0.9",
    "python-dotenv>=1.0.0",
    "apscheduler>=3.10.0",
    "jinja2>=3.1.0",
    "requests>=2.31.0",
    "pysnmp-lextudio>=6.0.0",
    "PyMySQL>=1.1.0",
]

setup(
    name="readiness-nms",
    version="2.0.0",
    author="Readiness NMS Team",
    author_email="admin@example.com",
    description="Full-stack Network, Server & Service Monitoring Suite (NMS) with Security Auditing & Performance Analytics",
    long_description=long_description,
    long_description_content_type="text/markdown",
    url="https://github.com/your-username/readiness-nms",
    packages=find_packages(),
    include_package_data=True,
    package_data={
        "app": ["templates/**/*.html", "static/**/*"],
    },
    classifiers=[
        "Development Status :: 5 - Production/Stable",
        "Intended Audience :: System Administrators",
        "Topic :: System :: Networking :: Monitoring",
        "Topic :: System :: Systems Administration",
        "Programming Language :: Python :: 3",
        "Programming Language :: Python :: 3.10",
        "Programming Language :: Python :: 3.11",
        "Programming Language :: Python :: 3.12",
        "Programming Language :: Python :: 3.14",
        "License :: OSI Approved :: MIT License",
        "Operating System :: OS Independent",
    ],
    python_requires=">=3.10",
    install_requires=requirements,
    entry_points={
        "console_scripts": [
            "readiness-nms=run:main",
        ],
    },
)
