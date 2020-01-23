FROM golang:1.13
WORKDIR /app
COPY main.go .
RUN go get github.com/gin-gonic/gin && go get github.com/gorilla/websocket
RUN go build -o main .

FROM ubuntu:18.04
WORKDIR /root
COPY --from=0 /app .
RUN apt-get update && apt-get install -y ca-certificates && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*
EXPOSE 8888
EXPOSE 8889
CMD ["./main", "-host", "streaming.vk.com", "-key", "012b590e020df5d880a6af8f2d6df2de8154d25d"]
