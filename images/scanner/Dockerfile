FROM golang:1.15 as builder

ARG app_env
ENV APP_ENV $app_env

COPY ./app /go/src/github.com/pilot114/scanner
WORKDIR /go/src/github.com/pilot114/scanner
RUN go get ./
RUN CGO_ENABLED=0 GOOS=linux go build -o app .

FROM alpine:latest
WORKDIR /root
COPY --from=builder /go/src/github.com/pilot114/scanner/app .
CMD [ "./app"]