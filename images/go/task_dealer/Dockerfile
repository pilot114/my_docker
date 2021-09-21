FROM golang:1.15 as builder

ARG app_env
ENV APP_ENV $app_env

COPY ./app /go/src/github.com/pilot114/task_dealer
WORKDIR /go/src/github.com/pilot114/task_dealer

RUN go get ./
RUN CGO_ENABLED=1 GOOS=linux go build -o app .

FROM ubuntu:18.04
WORKDIR /root
COPY --from=builder /go/src/github.com/pilot114/task_dealer/app .
CMD [ "./app"]